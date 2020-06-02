<?php


namespace Knuckles\Scribe\Extracting\Strategies\Postman;


use Illuminate\Routing\Route;
use Knuckles\Scribe\Extracting\RouteDocBlocker;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use Mpociot\Reflection\DocBlock\Tag;
use ReflectionClass;
use ReflectionFunctionAbstract;
use Mpociot\Reflection\DocBlock;

class GetPostmanEventsStrategy extends Strategy
{
    public $stage = 'postmanEvents';

    public function __invoke(Route $route, ReflectionClass $controller, ReflectionFunctionAbstract $method, array $routeRules, array $alreadyExtractedData = [])
    {
        $docBlocks = RouteDocBlocker::getDocBlocksFromRoute($route);
        /** @var DocBlock $methodDocBlock */
        $methodDocBlock = $docBlocks['method'];
        $classDocBlock  = $docBlocks['class'];

        return $this->getMetadataFromDocBlock($methodDocBlock, $classDocBlock);
    }

    public function getMetadataFromDocBlock(DocBlock $methodDocBlock, DocBlock $classDocBlock): array
    {
        return [
            'postmanEvent' => $this->getPostmanEventFromDocBlock($classDocBlock->getTags()) ?: $this->getPostmanEventFromDocBlock($methodDocBlock->getTags()),
        ];
    }


    /**
     * @param array $tags Tags in the method doc block
     *
     * @return Tag
     */
    protected function getPostmanEventFromDocBlock(array $tags)
    {
        $event = collect($tags)
            ->first(function($tag) {
                return $tag instanceof Tag && strtolower($tag->getName()) === 'postmanevent';
            });
        return $event ? $event->getDescription() : null;
    }

}
