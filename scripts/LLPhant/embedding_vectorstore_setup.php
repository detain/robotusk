<?php

use LLPhant\OpenAIConfig;
use LLPhant\Chat\OpenAIChat;
//use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3LargeEmbeddingGenerator;
//use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3SmallEmbeddingGenerator;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAIADA002EmbeddingGenerator;
//use LLPhant\Embeddings\VectorStores\FileSystem\FileSystemVectorStore;
//use LLPhant\Embeddings\VectorStores\Redis\RedisVectorStore;
//use LLPhant\Embeddings\VectorStores\Qdrant\QdrantVectorStore;
use LLPhant\Embeddings\VectorStores\ChromaDB\ChromaDBVectorStore;
use Tests\Integration\Embeddings\VectorStores\Doctrine\PlaceEntity;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingFormatter\EmbeddingFormatter;
//use Qdrant\Qdrant;
//use Qdrant\Config;
//use Qdrant\Http\Builder;

include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../vendor/theodo-group/llphant/tests/Integration/Embeddings/VectorStores/Doctrine/PlaceEntity.php';

//$qdrantConfig = new Config('127.0.0.1');
//$qdrantConfig->setApiKey(QDRANT_API_KEY);
//$client = new Predis\Client('tcp://216.158.226.14:6379');

$config = new OpenAIConfig();
$config->apiKey = '-';
$config->url = 'http://skynet.interserver.net:8080/v1';
$config->model = 'text-embedding-ada-002';

$validExtensions = ['php'];
$filePath = __DIR__.'/PlacesTextFiles';
$filePath = '/home/my/include';
$reader = new FileDataReader($filePath, PlaceEntity::class, $validExtensions);
//$reader = new FileDataReader($filePath, Document::class, $validExtensions);
$documents = $reader->getDocuments();
// The embeddings models have a limit of string size that they can process. To avoid this problem we split the document into smaller chunks. The DocumentSplitter class is used to split the document into smaller chunks.
$splitDocuments = DocumentSplitter::splitDocuments($documents, 800);
// The EmbeddingFormatter is an optional step to format each chunk of text into a format with the most context. Adding a header and links to other documents can help the LLM to understand the context of the text.
$formattedDocuments = EmbeddingFormatter::formatEmbeddings($splitDocuments);
echo "Embedding\n";
// This is the step where we generate the embedding for each chunk of text by calling the LLM.
//$embeddingGenerator = new OpenAI3SmallEmbeddingGenerator($config);
$embeddingGenerator = new OpenAIADA002EmbeddingGenerator($config);
$embeddedDocuments = $embeddingGenerator->embedDocuments($formattedDocuments);
// You can also create a embedding from a text using the following code:
//$embedding = $embeddingGenerator->embedText('I love food');
echo "Vector storing\n";
// Once you have embeddings you need to store them in a vector store. The vector store is a database that can store vectors and perform a similarity search. There are currently these vectorStore classes:
//$vectorStore = new DoctrineVectorStore($entityManager, PlaceEntity::class);
//$vectorStore = new FileSystemVectorStore();
$vectorStore = new ChromaDBVectorStore();
//$vectorStore = new RedisVectorStore($client);
/*$collectionName = 'places2';
$vectorStore = new QdrantVectorStore($qdrantConfig, $collectionName);
$vectorStore->createCollectionIfDoesNotExist($collectionName, $embeddingGenerator->getEmbeddingLength());*/
$vectorStore->addDocuments($embeddedDocuments);
echo "done\n";
