<?php

namespace ConverteCTeNFe;

use Exception;

class ConverteXml
{
    /**
     * Converte um XML de NFe ou CTe em um objeto SimpleXMLElement.
     *
     * @param string $xmlPath Caminho completo do arquivo XML.
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function xmlNFeToObject(string $xmlPath): object
    {
        try {
            if (!file_exists($xmlPath)) {
                throw new Exception("Arquivo XML não encontrado: {$xmlPath}");
            }

            $xmlContent = file_get_contents($xmlPath);

            $xmlContent = trim($xmlContent);

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent, "SimpleXMLElement", LIBXML_NOCDATA);

            if ($xml === false) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                throw new Exception("Erro ao carregar XML: " . print_r($errors, true));
            }

            return $xml;
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }
}
