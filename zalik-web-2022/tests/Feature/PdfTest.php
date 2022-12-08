<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PdfTest extends TestCase
{
    use WithoutMiddleware;

    protected $fileType = "pdf";
    protected $fields = ['id', 'filename', 'file_size'];
    protected $modelName = "file";

    public function testUploadOk()
    {
        Storage::fake('local');
        $fileName = Str::random() . "." .$this->fileType;
        $size =  random_int(1024,2048);
        $file = UploadedFile::fake()->create($fileName, $size);
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => $file]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => $this->fields
        ]);
        Storage::assertExists($fileName);
        $this->assertDatabaseHas(Str::plural($this->modelName), ['filename' => $fileName]);
    }

    public function testUploadBadFileType()
    {
        $file = UploadedFile::fake()->create("test.txt");
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => '']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => $file]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUploadBEmptyRequest()
    {
        $file = UploadedFile::fake()->create("test.txt");
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => '']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => $file]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
