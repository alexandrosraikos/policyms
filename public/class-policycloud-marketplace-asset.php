<?php

class PolicyCloud_Marketplace_Asset
{
    public string $id;
    public string $category;

    public string $filename;
    public string $checksum;
    public string $size;
    public string $update_date;
    public int $version;
    public int $downloads;


    public function __construct(string $id, string $category, array $metadata)
    {
        $this->id = $id;
        $this->category = $category;

        $this->filename = $metadata['filename'];
        $this->checksum = $metadata['md5'];
        $this->size = $metadata['size'];
        $this->update_date = $metadata['updateDate'];
        $this->version = $metadata['version'];
        $this->downloads = $metadata['downloads'];
    }

    public function update(string $file_identifier): void
    {
        $token = PolicyCloud_Marketplace_Account::retrieve_token();

        self::handle_retrieved_file(
            $file_identifier,
            $this->category,
            function ($file) use ($token) {
                PolicyCloud_Marketplace::api_request(
                    'PUT',
                    '/assets/' . $this->category . '/' . $this->id,
                    [
                        'asset' => new CURLFile($file['path'], $file['mimetype'], $file['name'])
                    ],
                    $token,
                    [
                        'x-filename: ' . $file['name'],
                        'x-mimetype: ' . $file['mimetype'],
                        'x-access-token: ' . $token
                    ],
                    true
                );
            }
        );
    }

    public function delete(): void
    {
        $token = PolicyCloud_Marketplace_Account::retrieve_token();

        PolicyCloud_Marketplace::api_request(
            'DELETE',
            '/assets/' . $this->category . '/' . $this->id,
            [],
            $token
        );
    }

    public function pull()
    {
        $token = PolicyCloud_Marketplace_Account::retrieve_token();

        // Currently only supports images.
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/images/' . $this->id,
            [],
            $token,
            [
                'Content-Type: application/octet-stream',
                (!empty($token) ? ('x-access-token: ' . $token) : null)
            ],
        );

        return $response;
    }

    public function get_download_url(): string
    {
        $token = PolicyCloud_Marketplace_Account::retrieve_token();

        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/assets/' . $this->category . '/' . $this->id,
            [],
            $token
        );

        return $response['otc'];
    }

    protected static function check_specs($type, array $file): void
    {
        switch ($type) {
            case 'images':
                if (
                    $file['mimetype'] != 'image/jpeg' &&
                    $file['mimetype'] != 'image/png'
                ) {
                    throw new PolicyCloudMarketplaceInvalidDataException(
                        "Supported formats for asset images are .png and .jpg/.jpeg."
                    );
                }
                break;
            case 'videos':
                if (
                    $file['mimetype'] != 'video/mp4' &&
                    $file['mimetype'] != 'video/ogg' &&
                    $file['mimetype'] != 'video/webm'
                ) {
                    throw new PolicyCloudMarketplaceInvalidDataException(
                        "Supported formats for asset videos are .mp4, .ogg and .webm."
                    );
                }
                break;
            default:
                throw new PolicyCloudMarketplaceInvalidDataException(
                    "There is no asset category of this type."
                );
                break;
        }
    }

    protected static function handle_retrieved_file(string $name, string $category, callable $completion)
    {


        if (empty($_FILES[$name])) {
            throw new PolicyCloudMarketplaceInvalidDataException(
                sprintf(
                    "The file %s has not been received.",
                    $name
                )
            );
        }

        // Check if multiple files were uploaded.
        if (array_keys($_FILES[$name]['name']) === range(0, count($_FILES[$name]['name']) - 1)) {
            $files = [];
            // Check for errors on each file before proceeding.
            foreach ($_FILES[$name]['error'] as  $key => $error) {
                // Throw on file error.
                if ($error != 0 && $error != 4) {
                    throw new PolicyCloudMarketplaceInvalidDataException(
                        "An error occured when uploading the new files: " . PolicyCloud_Marketplace::fileUploadErrorInterpreter($error)
                    );
                }

                $file = [
                    'path' => $_FILES[$name]['tmp_name'][$key],
                    'mimetype' => $_FILES[$name]['tmp_name'][$key],
                    'name' => $_FILES[$name]['name'][$key]
                ];

                // Throw on incompatible specs.
                self::check_specs($category, $file);

                // Add accepted file to array.
                array_push($files, $file);
            }

            // Get data and run completion.
            foreach ($files as $file) {
                $completion($file);
            }
        } else {
            $error = $_FILES[$name]['error'];
            if ($error == 0 && $error == 4) {
                $file = [
                    'path' => $_FILES[$name]['tmp_name'],
                    'mimetype' => $_FILES[$name]['tmp_name'],
                    'name' => $_FILES[$name]['name']
                ];
                self::check_specs($category, $file);
                $completion($file);
            } else {
                throw new PolicyCloudMarketplaceInvalidDataException(
                    "An error occured when uploading the new file: " . PolicyCloud_Marketplace::fileUploadErrorInterpreter($error)
                );
            }
        }
    }

    public static function create(string $name, PolicyCloud_Marketplace_Description $description, int $index = null): void
    {
        $token = PolicyCloud_Marketplace_Account::retrieve_token();
        $category = $name;

        // Handle file whether array or singular ID.
        self::handle_retrieved_file(
            $name,
            $category,
            function ($file) use ($category, $token, $description) {

                PolicyCloud_Marketplace::api_request(
                    'POST',
                    '/assets/' . $category . '/' . $description->id,
                    [
                        'asset' => new CURLFile($file['path'], $file['mimetype'], $file['name'])
                    ],
                    $token,
                    [
                        'x-access-token: ' . $token,
                        'x-filename: ' . $file['name'],
                        'x-mimetype: ' . $file['mimetype']
                    ],
                    true
                );
            },
            $index ?? null
        );
    }
}
