<?php

namespace Precifier;

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\Sdk;

class Schema
{
    /**
     * @return \Aws\DynamoDb\DynamoDbClient
     */
    private static function configure()
    {
        $sdk = new Sdk([
            'credentials' => [
                'key'   => 'AKIAJHRHRLBYEVQBSAJA',
                'secret'   => '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',
            ],
            'region'   => 'sa-east-1',
            'version'  => 'latest'
        ]);

        return $sdk->createDynamoDb();
    }

    public static function createTable($newTable, $properties, int $readCapacityUnits = 10, int $writeCapacityUnits = 10)
    {
        $dynamodb = self::configure();

        $params = [
            'TableName' => $newTable,
            'KeySchema' => [],
            'AttributeDefinitions' => [],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => $readCapacityUnits,
                'WriteCapacityUnits' => $writeCapacityUnits
            ]
        ];

        foreach ($properties as $property) {
            $params['KeySchema'][] = [
                'AttributeName' => $property['attribute'],
                'KeyType' => $property['keyType']
            ];
            $params['AttributeDefinitions'][] = [
                'AttributeName' => $property['attribute'],
                'AttributeType' => $property['type']
            ];
        }

        try {
            $result = $dynamodb->createTable($params);

            $status = $result->get('@metadata')['statusCode'];
        } catch (DynamoDbException $e) {
            $status = $e->getStatusCode();
        }

        return ['status' => $status];
    }

    public static function updateTable($table, $properties, int $readCapacityUnits = 10, int $writeCapacityUnits = 10)
    {
        $dynamodb = self::configure();

//        $params = [
//            'TableName' => $newTable,
//            'KeySchema' => [],
//            'AttributeDefinitions' => [],
//            'ProvisionedThroughput' => [
//                'ReadCapacityUnits' => $readCapacityUnits,
//                'WriteCapacityUnits' => $writeCapacityUnits
//            ]
//        ];

//        foreach ($properties as $property) {
//            $params['KeySchema'][] = [
//                'AttributeName' => $property['attribute'],
//                'KeyType' => $property['keyType']
//            ];
//            $params['AttributeDefinitions'][] = [
//                'AttributeName' => $property['attribute'],
//                'AttributeType' => $property['type']
//            ];
//        }

        $params = [
            "AttributeDefinitions" => [
                [
                    "AttributeName" => "year2",
                    "AttributeType" => "N"
                ],
                [
                    "AttributeName" => "title",
                    "AttributeType" => "S"
                ],
            ],
            "GlobalSecondaryIndexUpdates" => [
                [
//                    "Create" => [
//                        "IndexName" => "year2",
//                        "KeySchema" => [
//                            [
//                                "AttributeName" => "year",
//                                "KeyType" => "HASH"
//                            ],
//                            [
//                                "AttributeName" => "title",
//                                "KeyType" => "RANGE"
//                            ],
//                        ],
//                        "Projection" => [
////                            "NonKeyAttributes" => [ "year", "title", "new" ],
////                            "ProjectionType" => "INCLUDE"
//                        ],
//                        "ProvisionedThroughput" => [
//                            "ReadCapacityUnits" => 10,
//                            "WriteCapacityUnits" => 10
//                        ]
//                    ],
//                    "Delete" => [
//                        "IndexName" => "title"
//                    ],
                    "Update" => [
                        "IndexName" => "title",
                        "ProvisionedThroughput" => [
                            "ReadCapacityUnits" => 10,
                            "WriteCapacityUnits" => 10
                        ]
                    ]
                ]
            ],
            "ProvisionedThroughput" => [
                "ReadCapacityUnits" => 10,
                "WriteCapacityUnits" => 10
            ],
            "TableName" => $table
        ];

        try {
            $result = $dynamodb->updateTable($params);

            $status = $result->get('@metadata')['statusCode'];
        } catch (DynamoDbException $e) {
            // print_r($e->getMessage());die;
            $status = $e->getStatusCode();
        }
        // print_r($status);die;

        return ['status' => $status];
    }

    public static function deleteTable($table)
    {
        $dynamodb = self::configure();

        try {
            $result = $dynamodb->deleteTable(["TableName" => $table]);

            var_dump($result);


            $status = $result->get('@metadata')['statusCode'];
        } catch (DynamoDbException $e) {
            print_r($e);die;

            $status = $e->getStatusCode();
        }
        die;

        return ['status' => $status];
    }

}