# SDK для работы с API Хранилища файлов Наложка.рф

- [Документация по API Хранилища файлов](https://api.nalogka.ru/misc/files.html)

## Использование

### Инициализация api-клиента и компонента сериализатора

```php
$serializationComponent = new SerializationComponent();

$apiClient = new ApiClient("https://sandbox.filestorage.api.nalogka.ru/", [
    'headers' => [
        'X-Nalogka-Auth-Token' => '9qASPlstioSjksdqpLkSF2js8Iks1CIv'
    ],
], $serializationComponent);
```

### Загрузка файла

```php
$fileContent = file_get_contents("/path/to/hello.txt");

$uploadRequest = (new FileUploadRequest($apiClient))
    ->fileName("hello.txt")
    ->description("Test file")
    ->file($fileContent);

try {
    $fileMetaData = $uploadRequest->request();
} catch (ApiErrorException $e) {
    // Ошибка от API
} catch (ServerErrorException $e) {
    // Неизвестный ответ от сервера
} catch (NalogkaSdkException $e) {
    // Ошибка в SDK, например проблема с десереализацией
}
```

### Запрос информации о ранее загруженном файле

```php
$metaDataRequest = (new FileGetRequest($apiClient))
    ->fileName("mqsyarul/hello.txt");

try {
    $fileMetaData = $metaDataRequest->request();
} catch (ApiErrorException $e) {
    // Ошибка от API
} catch (ServerErrorException $e) {
    // Неизвестный ответ от сервера
} catch (NalogkaSdkException $e) {
    // Ошибка в SDK, например проблема с десереализацией
}
```

### Создание временного токена доступа

```php
$temporaryTokenRequest = (new TemporaryAccessTokenRequest($apiClient))
    ->endpoint("POST /form-upload");

try {
    $temporaryToken = $temporaryTokenRequest->request();
} catch (ApiErrorException $e) {
    // Ошибка от API
} catch (ServerErrorException $e) {
    // Неизвестный ответ от сервера
} catch (NalogkaSdkException $e) {
    // Ошибка в SDK, например проблема с десереализацией
}
```
