# CloudZ

## Bem-vindo ao CloudZ Library
O CloudZ é uma biblioteca PHP desenvolvida para facilitar a interação com diferentes serviços de armazenamento em nuvem através de uma interface unificada e intuitiva.

## Serviços Suportados
- **FTP** - File Transfer Protocol
- **SFTP** - SSH File Transfer Protocol  
- **AWS S3** - Amazon Simple Storage Service
- **Google Drive** - Google Drive API

## Instalação
A biblioteca CloudZ está disponível via Composer:

```bash
composer require caiovieiraflor/cloudz
```

## Inicialização

Para usar a biblioteca CloudZ, você deve fornecer os dados da conta diretamente ao instanciar o serviço:

```php
use CloudZ\CloudService;
use CloudZ\CloudServiceTypes;

// Dados da conta FTP
$accountData = [
    'host' => '192.168.0.1',
    'port' => 21,
    'user' => 'admin',
    'password' => '123456',
    'isPassive' => true,
    'dirWork' => '/uploads',
    'urlAccess' => 'http://example.com/uploads',
    'useSSH' => false
];

$cloudService = new CloudService(CloudServiceTypes::FTP_ACCOUNT, $accountData);
```

### Estrutura de Dados por Serviço

#### FTP/SFTP
```php
$ftpData = [
    'host' => '192.168.0.1',
    'port' => 21,              // 21 para FTP, 22 para SFTP
    'user' => 'username',
    'password' => 'password',
    'isPassive' => true,       // true/false
    'dirWork' => '/uploads',
    'urlAccess' => 'http://example.com/uploads',
    'useSSH' => false          // false para FTP, true para SFTP
];
```

#### AWS S3
```php
$s3Data = [
    'AWSKey' => 'YOUR_ACCESS_KEY',
    'AWSSecretKey' => 'YOUR_SECRET_KEY', 
    'AWSRegion' => 'us-east-1',
    'AWSType' => 'S3',
    'bucketName' => 'my-bucket'
];
```

#### Google Drive
```php
$driveData = [
    'clientId' => 'your_google_client_id.apps.googleusercontent.com',
    'clientSecret' => 'your_google_client_secret',
    'refreshToken' => 'your_refresh_token',
    'folderId' => null,              // ou ID da pasta específica
    'type' => 'GOOGLE-DRIVE'
];
```

## Tipos de Serviços
Use as constantes da classe `CloudServiceTypes`:

```php
use CloudZ\CloudServiceTypes;

// Constantes disponíveis
CloudServiceTypes::FTP_ACCOUNT        // 'FTP'
CloudServiceTypes::AWS_S3_ACCOUNT     // 'AWS-S3'  
CloudServiceTypes::GOOGLE_DRIVE_ACCOUNT // 'GOOGLE-DRIVE'
```

## Configurações

A classe `CloudService` permite configurações opcionais através da propriedade `settings`:

```php
use CloudZ\CloudService;
use CloudZ\CloudServiceTypes;

$cloudService = new CloudService(CloudServiceTypes::FTP_ACCOUNT, $accountData);

// Configurações disponíveis
$cloudService->settings->add('canEncryptName', true);      // Criptografa o nome do arquivo
$cloudService->settings->add('canDeleteAfterUpload', true); // Remove arquivo local após upload
$cloudService->settings->add('path', '/custom/directory');  // Define diretório específico
```

### Configurações Disponíveis
- **canEncryptName** (bool): Criptografa o nome do arquivo durante o envio
- **canDeleteAfterUpload** (bool): Remove o arquivo local após upload bem-sucedido
- **path** (string): Define um diretório específico no serviço de nuvem

## Utilitários de Caminho

A biblioteca oferece utilitários para organização de diretórios:

```php
use CloudZ\Utility\Path\CloudServicePathUtility;

// Para projetos de solução
$solutionPath = CloudServicePathUtility::mountSolutionPath('root', 'meuProjeto', 'modulo');
// Resultado: 'root/solucoes/meuProjeto/modulo/'

// Para integrações externas  
$integrationPath = CloudServicePathUtility::mountIntegrationPath('root', 'minhaIntegracao');
// Resultado: 'root/integracoes/minhaIntegracao/'

// Usando com configurações
$cloudService->settings->add('path', $solutionPath);
```

## Operações com Arquivos

### Upload de Arquivos

```php
use CloudZ\CloudService;
use CloudZ\CloudServiceFile;
use CloudZ\CloudServiceTypes;

$cloudService = new CloudService(CloudServiceTypes::FTP_ACCOUNT, $accountData);

// Preparar arquivo
$file = new CloudServiceFile('C:\Documents\exemplo.txt');

// Realizar upload
$response = $cloudService->upload($file);

// Verificar resultado
if ($response->getCode() === 200) {
    echo "Upload realizado com sucesso!";
    echo "URL de acesso: " . $response->getUrl();
} else {
    echo "Erro no upload: " . $response->getMessage();
}
```

### Exclusão de Arquivos

```php
use CloudZ\DeleteCloudServiceFile;

// Criar referência do arquivo remoto pela URL
$remoteFile = new DeleteCloudServiceFile('http://example.com/uploads/arquivo.txt');

// Realizar exclusão
$response = $cloudService->delete($remoteFile);

// Verificar resultado
if ($response->getCode() === 200) {
    echo "Arquivo excluído com sucesso!";
} else {
    echo "Erro na exclusão: " . $response->getMessage();
}
```

## Respostas da API

Todas as operações retornam objetos de resposta com informações estruturadas:

### Resposta de Sucesso
```php
// Propriedades disponíveis
$response->getCode();     // 200 para sucesso
$response->getMessage();  // Mensagem de sucesso
$response->getUrl();      // URL de acesso (apenas para upload)
```

### Resposta de Erro
```php
// Propriedades disponíveis  
$response->getCode();     // 400 para erro
$response->getMessage();  // Descrição do erro
```

## Exemplos Completos

### Exemplo FTP com Configurações
```php
use CloudZ\CloudService;
use CloudZ\CloudServiceFile;
use CloudZ\CloudServiceTypes;
use CloudZ\Utility\Path\CloudServicePathUtility;

// Dados da conta FTP
$ftpAccount = [
    'host' => 'ftp.example.com',
    'port' => 21,
    'user' => 'username',
    'password' => 'password',
    'isPassive' => true,
    'dirWork' => '/public_html',
    'urlAccess' => 'https://example.com',
    'useSSH' => false
];

// Criar serviço
$cloudService = new CloudService(CloudServiceTypes::FTP_ACCOUNT, $ftpAccount);

// Configurar caminho organizado
$projectPath = CloudServicePathUtility::mountSolutionPath('uploads', 'meuSistema', 'documentos');
$cloudService->settings->add('path', $projectPath);
$cloudService->settings->add('canEncryptName', true);
$cloudService->settings->add('canDeleteAfterUpload', false);

// Upload
$file = new CloudServiceFile('/local/path/documento.pdf');
$uploadResponse = $cloudService->upload($file);

if ($uploadResponse->getCode() === 200) {
    echo "Arquivo enviado: " . $uploadResponse->getUrl();
}
```

### Exemplo AWS S3
```php
use CloudZ\CloudService;
use CloudZ\CloudServiceFile;
use CloudZ\CloudServiceTypes;

// Dados da conta AWS S3
$s3Account = [
    'AWSKey' => 'YOUR_ACCESS_KEY',
    'AWSSecretKey' => 'YOUR_SECRET_KEY',
    'AWSRegion' => 'us-east-1',
    'AWSType' => 'S3',
    'bucketName' => 'my-application-bucket'
];

// Criar serviço
$cloudService = new CloudService(CloudServiceTypes::AWS_S3_ACCOUNT, $s3Account);

// Configurar pasta específica
$cloudService->settings->add('path', 'user-uploads/images/');

// Upload
$file = new CloudServiceFile('/local/images/photo.jpg');
$response = $cloudService->upload($file);

if ($response->getCode() === 200) {
    echo "Imagem disponível em: " . $response->getUrl();
}
```

### Exemplo Google Drive
```php
use CloudZ\CloudService;
use CloudZ\CloudServiceFile;
use CloudZ\CloudServiceTypes;
use CloudZ\GoogleDrive\GoogleDriveHelper;

// Dados da conta Google Drive
$driveAccount = [
    'clientId' => 'your_client_id.apps.googleusercontent.com',
    'clientSecret' => 'your_client_secret',
    'refreshToken' => 'your_refresh_token',
    'folderId' => null,
    'type' => 'GOOGLE-DRIVE'
];

// Criar serviço
$cloudService = new CloudService(CloudServiceTypes::GOOGLE_DRIVE_ACCOUNT, $driveAccount);

// Opcional: Usar GoogleDriveHelper para gerenciar pastas
$googleDriveHelper = new GoogleDriveHelper($cloudService->account);

// Testar conexão
if (!$googleDriveHelper->testConnection()) {
    echo "❌ Erro na conexão com Google Drive!";
    exit;
}

// Criar ou encontrar pasta
$pastaId = $googleDriveHelper->findFolderByName('MeusProjetos');
if (!$pastaId) {
    $pastaId = $googleDriveHelper->createFolder('MeusProjetos');
}

// Definir pasta de destino
$cloudService->account->folderId = $pastaId;

// Configurações adicionais
$cloudService->settings->add('makePublic', true);

// Upload
$file = new CloudServiceFile('/local/documents/report.docx');
$response = $cloudService->upload($file);

if ($response->getCode() === 200) {
    echo "Documento no Google Drive: " . $response->getUrl();
}
```

## Ferramentas e Utilitários

### ArrayHelper
Utilitário para manipulação de arrays (localizado em `CloudZ\Utility\ArrayHelper`).

### CloudServiceUtility
Classe base para utilitários da biblioteca (localizado em `CloudZ\Utility\CloudServiceUtility`).

### Validação de Contas
A biblioteca inclui validadores automáticos para cada tipo de serviço:
- `AWSS3AccountValidationStrategy`
- `FtpAccountValidationStrategy` 
- `GoogleDriveAccountValidationStrategy`

## Arquitetura

### Padrões Implementados
- **Strategy Pattern**: Para diferentes serviços de nuvem
- **Factory Pattern**: Para criação de contas e estratégias
- **Builder Pattern**: Para construção de objetos de conta

### Estrutura de Classes Principais
- `CloudService`: Classe principal da biblioteca
- `CloudServiceFile`: Representa arquivos locais
- `DeleteCloudServiceFile`: Representa arquivos remotos para exclusão
- `CloudServiceSettings`: Gerencia configurações
- `CloudServiceTypes`: Constantes dos tipos de serviço

## Requisitos
- PHP 7.4 ou superior
- Composer
- Extensões PHP necessárias conforme o serviço:
  - `curl` para AWS S3 e Google Drive
  - `ftp` para FTP
  - `ssh2` para SFTP

## Licença
Este projeto está licenciado sob os termos especificados no arquivo LICENSE.

## Autores
- **Caio Flor** - caio.flor@alunos.fho.edu.br
- **Lucas Soares** - lucassoares12016@alunos.fho.edu.br