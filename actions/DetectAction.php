<?php

class DetectAction implements ActionInterface
{
    public function execute(array $request)
    {
        $targetURL = $request['url'] ?? null;
        $format = $request['format'] ?? null;

        if (!$targetURL) {
            throw new \Exception('You must specify a url!');
        }
        if (!$format) {
            throw new \Exception('You must specify a format!');
        }

        $bridgeFactory = new BridgeFactory();

        foreach ($bridgeFactory->getBridgeClassNames() as $bridgeClassName) {
            if (!$bridgeFactory->isEnabled($bridgeClassName)) {
                continue;
            }

            $bridge = $bridgeFactory->create($bridgeClassName);

            $bridgeParams = $bridge->detectParameters($targetURL);

            if (is_null($bridgeParams)) {
                continue;
            }

            $bridgeParams['bridge'] = $bridgeClassName;
            $bridgeParams['format'] = $format;

            $url = '?action=display&' . http_build_query($bridgeParams);
            return new Response('', 301, ['location' => $url]);
        }

        throw new \Exception('No bridge found for given URL: ' . $targetURL);
    }
}
