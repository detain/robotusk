<?php

use LLPhant\OpenAIConfig;
use LLPhant\Chat\OpenAIChat;
//use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3LargeEmbeddingGenerator;
//use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3SmallEmbeddingGenerator;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAIADA002EmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\FileSystem\FileSystemVectorStore;
use Tests\Integration\Embeddings\VectorStores\Doctrine\PlaceEntity;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingFormatter\EmbeddingFormatter;
use LLPhant\Query\SemanticSearch\SiblingsDocumentTransformer;
use LLPhant\Query\SemanticSearch\QuestionAnswering;

include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../vendor/theodo-group/llphant/tests/Integration/Embeddings/VectorStores/Doctrine/PlaceEntity.php';

$config = new OpenAIConfig();
$config->apiKey = '-';
$config->url = 'http://skynet.interserver.net:8080/v1';
$config->model = 'gpt-4';

$validExtensions = ['php'];
$filePath = __DIR__.'/PlacesTextFiles';
$filePath = '/home/my/include';
echo "Reading Docs\n";
$reader = new FileDataReader($filePath, PlaceEntity::class, $validExtensions);
//$reader = new FileDataReader($filePath, Document::class, $validExtensions);
$documents = $reader->getDocuments();
// The embeddings models have a limit of string size that they can process. To avoid this problem we split the document into smaller chunks. The DocumentSplitter class is used to split the document into smaller chunks.
echo "SPlitting\n";
$splitDocuments = DocumentSplitter::splitDocuments($documents, 800);
// The EmbeddingFormatter is an optional step to format each chunk of text into a format with the most context. Adding a header and links to other documents can help the LLM to understand the context of the text.
echo "Formatting\n";
$formattedDocuments = EmbeddingFormatter::formatEmbeddings($splitDocuments);
// This is the step where we generate the embedding for each chunk of text by calling the LLM.
//$embeddingGenerator = new OpenAI3SmallEmbeddingGenerator($config);
echo "Embedding\n";
$embeddingGenerator = new OpenAIADA002EmbeddingGenerator($config);
$embeddedDocuments = $embeddingGenerator->embedDocuments($formattedDocuments);
// You can also create a embedding from a text using the following code:
//$embedding = $embeddingGenerator->embedText('I love food');
// Once you have embeddings you need to store them in a vector store. The vector store is a database that can store vectors and perform a similarity search. There are currently these vectorStore classes:
//$vectorStore = new DoctrineVectorStore($entityManager, PlaceEntity::class);
echo "storing vectors\n";
$vectorStore = new FileSystemVectorStore();
$vectorStore->addDocuments($embeddedDocuments);
// Get a context of 3 documents around the retrieved chunk
$siblingsTransformer = new SiblingsDocumentTransformer($vectorStore, 3);
$embeddingGenerator = new OpenAIADA002EmbeddingGenerator($config);
echo "q/a\n";
$qa = new QuestionAnswering(
    $vectorStore,
    $embeddingGenerator,
    new OpenAIChat($config),
    retrievedDocumentsTransformer: $siblingsTransformer
);
echo "answer\n";
$answer = $qa->answerQuestion('How do i update the vps_ips table?');
print_r($answer);
