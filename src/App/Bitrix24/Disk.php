<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait Disk
 * Methods for working with disc in Bitrix24.
 * 
 * @author    vladi-ri
 * @copyright 2024 vladi-ri
 * @see       https://github.com/vladi-ri/bitrix24-api
 * @license   OpenSource
 *
 * @version 1.0.0
 * 
 * v1.0.0 (17.02.2024) Introduce Bitrix24API PHP project
 */
trait Disk
{
    /**
     * Return a list of available storages
     * 
     * Action: 'disk.storage.getlist'
     * @see    https://training.bitrix24.com/rest_help/disk/storage/disk_storage_getlist.php
     * 
     * @param  array $filter Filtering parameters
     * 
     * @access public
     * @return Generator
     */
    public function getDiskStorageList(array $filter = []) : Generator {
        $params = [
            'filter'  => $filter
        ];

        return $this->getList('disk.storage.getlist', $params);
    }

    /**
     * Return a list of files and folders that are directly in the root of the repository.
     * 
     * Action: 'disk.storage.getchildren'
     * @see    https://training.bitrix24.com/rest_help/disk/storage/disk_storage_getchildren.php
     * 
     * @param  int|string $storageID Id хранилища
     * @param  array      $filter    Параметры
     *                               фильтрации
     * @return array
     */
    public function getDiskStorageChildren(int|string $storageID, array $filter = []) : array {
        $result = $this->request(
            'disk.storage.getchildren', [
                'id'     => $storageID,
                'filter' => $filter
            ]
        );

        return $result;
    }

    /**
     * Upload the new file to the specified folder on Disk.
     * 
     * Action: 'disk.folder.uploadfile'
     * @see    https://training.bitrix24.com/rest_help/disk/folder/disk_folder_uploadfile.php
     * @see    https://dev.1c-bitrix.ru/rest_help/disk/folder/disk_folder_uploadfile.php
     * 
     * @param  int|string $folderID         Folder ID
     * @param  string     $fileContent      Raw file data
     * @param  array      $data             Array of parameters describing the file (mandatory field NAME - name of the new file)
     * @param  bool       $isBase64FileData Is raw file data base64 encoded?
     * 
     * @access public
     * @return array
     */
    public function uploadfileDiskFolder(
        int|string $folderID,
        string $fileContent,
        array $data,
        bool $isBase64FileData = true
    ) : array {

        if (! $isBase64FileData) {
            $fileContent = base64_encode($fileContent);
        }

        $result = $this->request(
            'disk.folder.uploadfile', [
                'id'          => $folderID,
                'fileContent' => $fileContent,
                'data'        => $data
            ]
        );

        return $result;
    }
}
