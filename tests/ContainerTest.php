<?php
namespace Itseasy\Websocket\Test;

use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testContainer()
    {
        $app = new Application([
            "config_path" => [
                __DIR__."/../config/*.config.php",
                __DIR__."/config/*.config.php"
            ],
        ]);
        $app->build();

        $entries = $app->getContainer()->getKnownEntryNames();
        foreach ($entries as $entry) {
            try {
                $object = $app->getContainer()->get($entry);
            } catch (Exception $e) {
                debug(sprintf("\nService : %s\n", $entry));
                debug(sprintf("ERROR : \n%s\n\n", $e->getMessage()));
                $object = null;
            }


            $this->assertEquals(is_object($object), true);
        }
    }
}
