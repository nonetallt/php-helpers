<?php

namespace Nonetallt\Helpers\Internet\Http\Statuses;

use Nonetallt\Helpers\Filesystem\JsonFileRepository;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpCodeNotFoundException;

class HttpStatusRepository extends JsonFileRepository
{
    CONST COLLECTION_TYPE = HttpStatus::class;

    private static $instance;

    public static function getInstance() : self
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $filepath = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/resources/internet/http/status_codes';
        parent::__construct($filepath);
    }

    public function getByCode(int $code) : HttpStatus
    {
        foreach($this->items as $item) {
            if($item->getCode() === $code) return $item;
        }

        throw new HttpCodeNotFoundException("Http code $code does not exist");
    }

    public function codeExists(int $code) : bool
    {
        try {
            $this->getByCode($code);
            return true;
        }
        catch(HttpCodeNotFoundException $e) {
            return false;
        }
    }

    /**
     * @override
     */
    protected function loadFile(string $filepath, array $decoded)
    {
        $decoded['code'] = (integer)basename($filepath, '.json');
        if($decoded['standard'] === null) $decoded['standard'] = '';
        $this->push(HttpStatus::fromArray($decoded));
    }
}
