<?php

class FileTest extends TestCase
{
    public function testIndex()
    {
        $this->route('get', 'api.v1.file.index');
        $this->assertResponseStatus(200);

        $responseFileCount = $this->decodeResponseJson()['pagination']['totalElements'];
        $fileCount = \OParl\Server\Model\File::count();

        $this->assertEquals($fileCount, $responseFileCount);
    }

    public function testShow()
    {
        // @fixme: Adjust this test to work with actual files which are not present yet
        $this->route('get', 'api.v1.file.show', [1]);
        $this->assertResponseStatus(404);
        $this->seeJson([
            "error" => [
                "message" => "The requested item in `File` does not exist.",
                "status"  => 404,
            ],
        ]);
    }

    public function testShowNotExistsError()
    {
        $this->route('get', 'api.v1.file.show', [0]);
        $this->assertResponseStatus(404);
        $this->seeJson([
            "error" => [
                "message" => "The requested item in `File` does not exist.",
                "status"  => 404,
            ],
        ]);
    }
}
