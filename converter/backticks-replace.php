<?php

function replaceCodeBlocksWithComments($directory)
{
    if (!is_dir($directory)) {
        echo "Invalid directory: $directory" . PHP_EOL;
        return;
    }

    $files = glob($directory . '/*.md');

    foreach ($files as $file) {
        $content = file_get_contents($file);
        if ($content === false) {
            echo "Failed to read file: $file" . PHP_EOL;
            continue;
        }

        // Replace fenced code blocks with HTML comments
        // $modifiedContent = preg_replace('/```\s*(.*?)\s*```/s', '<!-- $1 -->', $content);

        // Replace minus surrounded by spaces or line breaks with triple minus
        $modifiedContent = preg_replace('/(?<=\s|^)\-(?=\s|$)/m', '---', $content);


        if ($modifiedContent !== null) {
            file_put_contents($file, $modifiedContent);
            echo "Modified: $file" . PHP_EOL;
        } else {
            echo "Error processing: $file" . PHP_EOL;
        }
    }
}

// Usage example
$directory = __DIR__; // Change this to your folder path
replaceCodeBlocksWithComments($directory);

?>
