<?php
namespace VPFramework\Utils;

class PHPFile{
    private $tokens = null;
    
    public function __construct($file)
    {
        $this->tokens = token_get_all(file_get_contents($file));
        if (1 === \count($this->tokens) && \T_INLINE_HTML === $this->tokens[0][0]) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not contain PHP code. Did you forgot to add the "<?php" start tag at the beginning of the file?', $file));
        }
    }

    /**
     * This function is a part of symfony/routing component
     * 
     * Returns the full class name for the first class in the file.
     *
     * @return string|false
     */
    public function findClass()
    {
        $class = false;
        $namespace = false;

        $nsTokens = [\T_NS_SEPARATOR => true, \T_STRING => true];
        if (\defined('T_NAME_QUALIFIED')) {
            $nsTokens[\T_NAME_QUALIFIED] = true;
        }
        for ($i = 0; isset($this->tokens[$i]); ++$i) {
            $token = $this->tokens[$i];
            if (!isset($token[1])) {
                continue;
            }

            if (true === $class && \T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }

            if (true === $namespace && isset($nsTokens[$token[0]])) {
                $namespace = $token[1];
                while (isset($this->tokens[++$i][1], $nsTokens[$this->tokens[$i][0]])) {
                    $namespace .= $this->tokens[$i][1];
                }
                $token = $this->tokens[$i];
            }

            if (\T_CLASS === $token[0]) {
                // Skip usage of ::class constant and anonymous classes
                $skipClassToken = false;
                for ($j = $i - 1; $j > 0; --$j) {
                    if (!isset($this->tokens[$j][1])) {
                        if ('(' === $this->tokens[$j] || ',' === $this->tokens[$j]) {
                            $skipClassToken = true;
                        }
                        break;
                    }

                    if (\T_DOUBLE_COLON === $this->tokens[$j][0] || \T_NEW === $this->tokens[$j][0]) {
                        $skipClassToken = true;
                        break;
                    } elseif (!\in_array($this->tokens[$j][0], [\T_WHITESPACE, \T_DOC_COMMENT, \T_COMMENT])) {
                        break;
                    }
                }

                if (!$skipClassToken) {
                    $class = true;
                }
            }

            if (\T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }
        return false;
    }
};