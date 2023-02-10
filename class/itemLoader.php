<?php

class ItemLoader implements Iterator
{
    public $producer;

    public function __construct(
        public string $remote_host = 'https://perdu.com',
        public array $ctx_option = [],
        public $position = 0,
        public $currentPage = 0,
        public bool $first_call = true,
        public $future = [],
        public $totalPages = 0
    ) {
        $this->producer = function (string $data) {
            // doing huge computation on data on another server
            // file_get_contents("/data/compute", stream_context_create(array_merge(["content"=>$data], $this->ctx_option)));
            
            
            // MOCKED
            sleep(rand(1, 2));
            return str_replace('amount of data', "amount of computed data\n", $data);
        };
    }

    private function externalCall($url, $currentPage )
    {
        // we should call external api here (MOCKED)
        //
        // $params = array('http' => array(
        //     'method' => 'POST',
        //     'content' => "page=$currentPage&size=PAGE_SIZE"
        // ));
        // $data = file_get_contents($url, stream_context_create(array_merge($params, $this->ctx_option)));
        return [
            'totalPages' => 42,
            'content' => [
                ['I should be a large amount of data'],
                ['I should be a large amount of data'],
                ['I should be a large amount of data'],
                ['I should be a large amount of data'],
                ['I should be a large amount of data']
            ]
        ];
    }
    public function load()
    {
        my_print("Doing stuff, calling external api");
        $data = $this->externalCall($this->remote_host . "/api/getHugeData", $this -> currentPage);

        if (http_response_code() == 429) //429 Restricted
        {
            throw new Exception("rate limited on page $this->currentPage");
            return false;
        } else {
            my_print("external api OK, compute datas in parallel");
            // total pages is found on the first call
            if ($this->first_call) {
                $this->totalPages = $data['totalPages'];
                $this->first_call = false;
            }
            foreach ($data['content'] as $currentData) {
                $this->future[] = (new \parallel\Runtime())->run($this->producer, $currentData);
            }
            $this->currentPage++;
            return true;
        }
    }
    public function current(): \parallel\Future
    {
        my_print("Current : item -> $this->position | page -> $this->currentPage");
        if ($this->position >= $this->currentPage * PAGE_SIZE) {
            $this->load();
        }

        return $this->future[$this->position];
    }
    public function rewind(): void
    {
        $this->position = 0;
    }
    public function key(): int
    {
        return $this->position;
    }
    public function next(): void
    {
        $this->position++;
    }
    public function valid(): bool
    {
        return ($this->position <= $this->totalPages);
    }
}
