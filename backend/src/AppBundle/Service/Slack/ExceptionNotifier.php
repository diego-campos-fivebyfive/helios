<?php

namespace AppBundle\Service\Slack;

/**
 * Send exception notifications to slack
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class ExceptionNotifier
{
    /**
     * @param \Exception $exception
     * @param string $environment
     */
    public function notify($exception, $environment)
    {
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 0 ;
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();

        $output = sprintf('\n\nException on %s ambience...\n', $environment);
        $output .= ':: INFO \n';
        $output .= sprintf('Message: %s \n', $message);
        $output .= sprintf('Status: %d \n', $statusCode);
        $output .= sprintf('File: %s \n', $file);
        $output .= sprintf('Line: %s \n', $line);
        $output .= '::TRACE \n';

        $traced = $this->formatTraceOutput($exception->getTrace());

        $output .= strlen($traced) ? $traced : 'No trace info.';
        $output .= '\n\n';

        $ambience = 'production' != $environment ? 'homolog' : 'production';

        exec("\${CLI_PATH}/ces-slack-notify --{$ambience} '$output'");
    }

    /**
     * @param array $trace
     * @return string
     */
    private function formatTraceOutput(array $trace)
    {
        $output = '';
        foreach ($trace as $invocked) {

            $info = '';
            foreach ($invocked as $definition => $value) {

                if ('class' == $definition) {
                    $namespace = explode('\\', $value)[0];
                    if (!in_array($namespace, ['App', 'AppBundle', 'AdminBundle', 'ApiBundle'])) {
                        break 2;
                    }
                }

                if ($value && $definition != 'type' && !is_array($value)) {

                    $tag = ucfirst($definition);
                    $str = str_replace('\\', '\\\\', is_string($value) ? $value : '');

                    if ($str) {
                        $info .= sprintf('%s: %s\n', $tag, $str);
                    }
                }
            }

            if (strlen($info)) {
                $output .= sprintf('%s\n', $info);
            }
        }

        return $output;
    }
}
