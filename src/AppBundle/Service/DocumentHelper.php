<?php

namespace AppBundle\Service;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Parameter;
use AppBundle\Entity\ParameterManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DocumentHelper
 * @package AppBundle\Service
 */
class DocumentHelper
{
    const TYPE_ACCOUNT = 'document-project-account';
    const TYPE_PROJECT = 'document-project-budget';

    const COVER_SIZE = 308000; // bytes == 300Kb
    const LOGO_SIZE  = 102400; // bytes == 100Kb

    /**
     * @var ParameterManager
     */
    private $parameterManager;
    /**
     * @var array
     */
    private $params;

    private $accountReference;

    private $projectReference;

    private $fixedSections = [
        'customer_data' => 'Dados do Cliente',
        //'consumption_data' => 'Dados de Consumo',
        'generation_data' => 'Dados da Geração',
        'financial_analysis' => 'Análise Financeira',
        //'graph_financial' => 'Gráfico Financeiro',
        'project_composition' => 'Equipamentos e Serviços'
    ];

    /**
     * @var array
     * Only Section Titles
     */
    private static $fontFamilies = [
        'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New',
        'Helvetica Neue', 'Helvetica', 'Impact', 'Lucida Grande',
        'Tahoma', 'Times New Roman', 'Verdana'
    ];

    /**
     * @var array
     * Only Section Titles
     */
    private static $fontSizes = [8, 9, 10, 11, 12, 13, 14, 15];

    private $mapping = [
        'type' => [],
        'cover_image' => [
            'types' => self::TYPE_ACCOUNT
        ],
        'header_logo' => [
            'types' => self::TYPE_ACCOUNT
        ],
        'header_text' => [
            'types' => self::TYPE_ACCOUNT
        ],
        'section_title_color' => [
            'default' => '#ffffff',
            'types' => self::TYPE_ACCOUNT
        ],
        'section_title_background' => [
            'default' => '#223544',
            'types' => self::TYPE_ACCOUNT
        ],
        'section_title_font_family' => [
            'default' => 'Arial',
            'types' => self::TYPE_ACCOUNT
        ],
        'section_title_font_size' => [
            'default' => 13,
            'types' => self::TYPE_ACCOUNT
        ],
        'chart_color' => [
            'default' => '#13b494',
            'types' => self::TYPE_ACCOUNT
        ],
        'sections' => [
            'default' => []
        ]
    ];

    /**
     * @var array
     */
    private $defaults = [
        'cover_file' => 'proposal_cover.jpg',
        'cover_name' => 'Default proposal cover',
        'logo_file' => 'proposal_logo.png',
        'logo_name' => 'Default proposal logo'
    ];

    private $errors = [];

    function __construct(ParameterManager $parameterManager, array $params = [])
    {
        $this->parameterManager = $parameterManager;
        $this->params = $params;
    }

    /**
     * @param $id
     * @return Parameter
     */
    public function load($id)
    {
        return $this->parameterManager->findOrCreate($id);
    }

    /**
     * @param BusinessInterface $account
     * @return Parameter
     */
    public function loadFromAccount(BusinessInterface $account)
    {
        $parameter = $this->load($this->generateId(self::TYPE_ACCOUNT, $account->getToken()));

        $parameter->set('type', self::TYPE_ACCOUNT);
        $parameter->set('owner', $account);

        $this->synchronize($parameter);

        if(!$parameter->getToken()){
            $this->save($parameter);
        }

        return $parameter;
    }

    /**
     * @param BusinessInterface $account
     * @return Parameter
     */
    public function redefineFromAccount(BusinessInterface $account)
    {
        $parameter = $this->load($this->generateId(self::TYPE_ACCOUNT, $account->getToken()));

        $this->parameterManager->delete($parameter);

        return $this->loadFromAccount($account);
    }

    /**
     * @param BusinessInterface $account
     */
    public function defaultFromAccount(BusinessInterface $account)
    {
        $parameter = $this->loadFromAccount($account);

        $this->generateDefaults($parameter);
    }

    /**
     * @param Parameter $parameter
     */
    private function generateDefaults(Parameter &$parameter)
    {
        $currentSections = $parameter->get('sections');
        $currentSections[2]['order'] = 5;
        $currentSections[3]['order'] = 8;

        $overrideSections = array_merge($currentSections, $this->getDefaultSections());

        $parameter->set('sections', $overrideSections);

        $this->synchronize($parameter);

        $commonDir = $this->params['root_dir'] . '/../storage/common/';
        $coverFile = $commonDir . $this->defaults['cover_file'];
        $logoFile = $commonDir . $this->defaults['logo_file'];

        if(file_exists($coverFile)){

            $file = new File($coverFile);
            $filename = $this->generateFilename($file);

            copy($file->getRealPath(), $this->getStorageDir() . $filename);

            $parameter->set('cover_image', $filename);
        }

        if(file_exists($logoFile)){

            $file = new File($logoFile);
            $filename = $this->generateFilename($file);

            copy($file->getRealPath(), $this->getStorageDir() . $filename);

            $parameter->set('header_logo', $filename);
        }

        $this->save($parameter);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     */
    public function handleRequest(FormInterface &$form, Request $request)
    {
        /** @var Parameter $parameter */
        $parameter = $form->getData();

        // Backup existent media files
        $cover = $parameter->get('cover_image');
        $logo = $parameter->get("header_logo");

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($parameter->has('cover_image'))
                $this->processCover($parameter, $cover);

            if($parameter->has('header_logo'))
                $this->processHeader($parameter, $logo);

            $this->save($parameter);
        }
    }

    /**
     * @param Parameter $parameter
     */
    public function save(Parameter $parameter)
    {
        /**
         * Prevent save duplicate entities by reference
         */
        if($this->projectReference && $this->accountReference){
            $this->parameterManager->getObjectManager()->detach($this->accountReference);
        }

        $parameter->remove('owner');

        $this->parameterManager->save($parameter);
    }

    /**
     * @param Parameter $parameter
     * @param File|null $previous
     */
    private function processCover(Parameter &$parameter, File $previous = null)
    {
        $maxSize = self::COVER_SIZE;
        $extensions = ['jpeg', 'jpg'];
        $cover = $parameter->get('cover_image');

        if($cover instanceof UploadedFile){

            if(!in_array($cover->guessExtension(), $extensions)){
                $this->errors[] = sprintf('A capa enviada não possui uma das extensões aceitas: %s', implode(',', $extensions));
            }

            if($maxSize && $cover->getSize() > $maxSize){
                $this->errors[] = sprintf('A capa enviada é maior que %sKb', $this->getCoverMaxSize());
            }

            if(!empty($this->errors)){
                return;
            }

            $parameter->set('cover_image', $this->upload($cover));
            if($previous)  unlink($previous->getRealPath());

        }elseif($previous){
            $parameter->set('cover_image', $previous->getFilename());
        }
    }

    /**
     * @param Parameter $parameter
     * @param File|null $previous
     */
    private function processHeader(Parameter &$parameter, File $previous = null)
    {
        $maxSize = self::LOGO_SIZE;
        $extensions = ['png', 'jpg', 'jpeg'];
        $logo = $parameter->get('header_logo');

        if($logo instanceof UploadedFile){

            if(!in_array($logo->guessExtension(), $extensions)){
                $this->errors[] = sprintf('A logo enviada não possui uma das extensões aceitas: %s', implode(',', $extensions));
            }

            if($maxSize && $logo->getSize() > $maxSize){
                $this->errors[] = sprintf('A logo enviada é maior que %sKb', $this->getLogoMaxSize());
            }

            if(!empty($this->errors)){
                return;
            }

            $logo = $this->upload($logo);

            if($previous) unlink($previous->getRealPath());

        }elseif($previous){
            $logo = $previous->getFilename();
        }

        $parameter->set('header_logo', $logo);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function upload(UploadedFile $file)
    {
        $filename = $this->generateFilename($file);

        $file->move($this->getStorageDir(), $filename);

        return $filename;
    }

    /**
     * @param $prefix
     * @param $suffix
     * @return string
     */
    private function generateId($prefix, $suffix)
    {
        return strtoupper(md5($prefix)) . '-' . $suffix;
    }

    public function removeFormFields(FormInterface &$form)
    {
        /** @var Parameter $parameter */
        $parameter = $form->getData();
        $currentType = $parameter->get('type');

        foreach($this->mapping as $field => $config){
            if(array_key_exists('types', $config)){
                $types = $config['types'];
                if((is_array($types) && !in_array($currentType, $types))
                    || ('*' != $types && $currentType != $types)) {
                    $form->get('parameters')->remove($field);
                }
            }
        }

        //dump($form->get); die;
    }

    public function createDocumentSettings(Parameter $accountDoc, Parameter $projectDoc)
    {
        $settings = $accountDoc->all();

        if(null != $cover = $accountDoc->get('cover_image')){
            if($cover instanceof File && $cover->isReadable()){
                $settings['cover_image'] = $cover->getRealPath();
            }
        }

        if(null != $logo = $accountDoc->get('header_logo')){
            if($logo instanceof File && $logo->isReadable()){
                $settings['header_logo'] = $logo->getRealPath();
            }
        }

        if(null != $headerText = $accountDoc->get('header_text')){
            $settings['header_text'] = nl2br($headerText);
        }

        $settings['sections'] = $projectDoc->get('sections');
        $settings['fixed_sections'] = $this->getFixedSectionsIdentities();

        return $settings;
    }

    /**
     * @param $filename
     * @return File
     */
    private function getFileInstance($filename)
    {
        return new File($this->getStorageDir() . $filename, false);
    }

    /**
     * @param File $file
     * @return string
     */
    private function generateFilename(File $file)
    {
        return md5(date('Y-m-d H:i:s')) . substr(md5(uniqid(time())), 0, 16) . '.' . $file->guessExtension();
    }

    /**
     * @param Parameter $parameter
     */
    private function synchronize(Parameter &$parameter)
    {
        foreach($this->mapping as $field => $config){
            $handler = array_key_exists('handler', $config)  ? $handler = $config['handler']
                : 'handle' . trim(str_replace(' ', '',ucwords(implode(' ', explode('_', $field)))));

            if(method_exists($this, $handler)){
                $this->$handler($parameter);
            }else{
                $parameter->set($field, array_key_exists('default', $config) ? $config['default'] : null);
            }
        }
    }

    /**
     * @return mixed
     */
    private function getStorageDir()
    {
        $dir = $this->params['storage'];
        if(!is_dir($dir)){
            mkdir($dir);
        }

        return $dir;
    }

    /**
     * @return array
     */
    public function getFixedSectionsIdentities()
    {
        $identities = [];
        foreach($this->fixedSections as $tag => $fixedSection){
            $key = trim(sprintf('[fixed_section][%s]', $tag));
            $identities[$key] = $fixedSection;
        }

        return $identities;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public static function getFontFamilies()
    {
        return array_combine(array_values(self::$fontFamilies), self::$fontFamilies);
    }

    /**
     * @return array
     */
    public static function getFontSizes()
    {
        return array_combine(array_values(self::$fontSizes), self::$fontSizes);
    }

    /**
     * @param array $sections
     */
    private function sortSections(array &$sections = [])
    {
        uasort($sections, function($a, $b){
            return $a['order'] > $b['order'];
        });

        foreach($sections as $key => $section){
            if(0 === strpos($section['content'], '[fixed_section]')){
                $sections[$key]['fixed'] = true;
            }else{
                $sections[$key]['fixed'] = false;
            }
        }
    }

    /**
     * |---------------------------------------------------------------------------|
     * | The following methods are called dynamically via sync, do not remove
     * |---------------------------------------------------------------------------|
     */

    /**
     * @param Parameter $parameter
     */
    public function handleType(Parameter &$parameter)
    {
        if(!$parameter->get('type')){
            dump($parameter); die;
        }
    }

    /**
     * Fake method calling
     */
    protected function resoleUnusedPrivateMethods()
    {
        $this->handleCoverImage();
        $this->handleHeaderLogo();
        $this->handleHeaderText();
        $this->handleSections();
        $this->handleSectionTitleBackground();
        $this->handleSectionTitleColor();
    }

    /**
     * @param Parameter $parameter
     */
    private function handleCoverImage(Parameter &$parameter)
    {
        if(null != $coverImage = $parameter->get('cover_image')){
            $parameter->set('cover_image', $this->getFileInstance($coverImage));
        }else{
            $parameter->set('cover_image', null);
        }
    }

    /**
     * @param Parameter $parameter
     */
    private function handleHeaderLogo(Parameter &$parameter)
    {
        if(null != $headerLogo = $parameter->get('header_logo')){
            $parameter->set('header_logo', $this->getFileInstance($headerLogo));
        }else{
            $parameter->set('header_logo', null);
        }
    }

    /**
     * @param Parameter $parameter
     */
    private function handleHeaderText(Parameter &$parameter)
    {
        if(!$parameter->get('header_text') && null != $owner = $parameter->get('owner')){

            $headerText = sprintf(
                "%s, %s, %s \n %s - %s. %s \n %s \n %s \n %s \n CNPJ: %s",
                $owner->getStreet() ?: '{logradouro}',
                $owner->getNumber() ?: '{numero}',
                $owner->getDistrict() ?: '{bairro}',
                $owner->getCity() ?: '{cidade}',
                $owner->getState() ?: '{estado}',
                $owner->getPostcode() ?: '{cep}',
                $owner->getPhone() ?: '{telefone}',
                $owner->getEmail() ?: '{email}',
                $owner->getWebsite() ?: '{website}',
                $owner->getDocument() ?: '{cnpj}'
            );

            $parameter->set('header_text', $headerText);
        }
    }

    /**
     * @param Parameter $parameter
     */
    private function handleSections(Parameter &$parameter)
    {
        $sections = $parameter->get('sections');

        if(!$sections) $sections = $this->mapping['sections']['default'];

        $count = 1;
        foreach($this->fixedSections as $tag => $label){
            $key = trim(sprintf('[fixed_section][%s]', $tag));
            $hit = false;
            foreach($sections as $section){
                if($section['content'] == $key){
                    $hit = true;
                }
            }

            if(!$hit){
                $sections[] = [
                    'title' => $label,
                    'content' => $key,
                    'order' => $count,
                    'fixed' => true
                ];
            }

            $count++;
        }

        $this->sortSections($sections);

        $parameter->set('sections', $sections);
    }

    /**
     * @param Parameter $parameter
     */
    private function handleSectionTitleBackground(Parameter &$parameter)
    {
        if(!$parameter->get('section_title_background')){
            $parameter->set('section_title_background', $this->mapping['section_title_background']['default']);
        }
    }

    /**
     * @param Parameter $parameter
     */
    private function handleSectionTitleColor(Parameter &$parameter)
    {
        if(!$parameter->get('section_title_color')){
            $parameter->set('section_title_color', $this->mapping['section_title_color']['default']);
        }
    }

    /**
     * @param Parameter $parameter
     */
    public function handleSectionTitleFontFamily(Parameter &$parameter)
    {
        if(!$parameter->get('section_title_font_family')){
            $parameter->set('section_title_font_family', $this->mapping['section_title_font_family']['default']);
        }
    }

    /**
     * @param Parameter $parameter
     */
    public function handleSectionTitleFontSize(Parameter &$parameter)
    {
        if(!$parameter->get('section_title_font_size')){
            $parameter->set('section_title_font_size', $this->mapping['section_title_font_size']['default']);
        }
    }

    /**
     * @param Parameter $parameter
     */
    public function handleChartColor(Parameter &$parameter)
    {
        if(!$parameter->get('chart_color')){
            $parameter->set('chart_color', $parameter->get('section_title_background'));
        }
    }

    /**
     * @return int
     */
    public function getCoverMaxSize()
    {
        return (int) floor(self::COVER_SIZE / 1024);
    }

    /**
     * @return int
     */
    public function getLogoMaxSize()
    {
        return (int) floor(self::LOGO_SIZE / 1024);
    }

    /**
     * @return array
     */
    private function getDefaultSections()
    {
        return [
            'fixed' => [
                'title' => 'Exemplo de Seção Personalizada Fixa',
                'content' => '<p>- O texto desta seção pode ser alterado em:</p><p>- Configuraçõees &gt;&gt; Proposta &gt;&gt; Seções<br></p>',
                'order' => 3,
                'fixed' => false,
                'editable' => false
            ],
            'pagebreak1' => [
                'title' => '',
                'content' => 'pagebreak',
                'order' => 4,
                'fixed' => false,
                'editable' => false
            ],
            'editable' => [
                'title' => 'Exemplo de Seção Personalizada Editável',
                'content' => '<p>- O texto desta seção pode ser editado no momento da emissão da proposta.</p> <p>- Você pode adicionar mais seções em Configurações &gt;&gt; Proposta &gt;&gt; Seções<br></p>',
                'order' => 6,
                'fixed' => false,
                'editable' => true
            ],
            'pagebreak2' => [
                'title' => '',
                'content' => 'pagebreak',
                'order' => 7,
                'fixed' => false,
                'editable' => false
            ],
        ];
    }
}