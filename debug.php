<?php
/**
 * Funções criadas para ajudar o desenvolvedor a visualizar e encontrar erros
 * @package FrameCalixto
 * @subpackage Debug
 */

/**
 * Função para debugar com exibição tipo var_dump
 * @param [mixed]
 * @return [string]
 */
function debug1($var) {
    ob_start();
    echo '<link rel="stylesheet" href="'.Productive\Http\Url::root().'/debug.css" />';
    echo '<div class="debug"><pre>';
    var_dump($var);
    echo '</pre></div>';
    echo ob_get_clean();
}

/**
 * Função para debugar com exibição lógica estrutural em tabelas
 * @param [mixed]
 * @param [metodos]
 * @param [visualizacao]
 * @return [string]
 */
function debug2($var, $metodos = true, $visualizacao = false) {
    echo '<link rel="stylesheet" href="'.Productive\Http\Url::root().'/debug.css" />';
    switch (true) {
        case is_bool($var):
            echo ($var ? '<font class="tipoPrimario" >(booleano)</font> = <font class="booleano" >true</font>' : '<font class="tipoPrimario">(booleano)</font> = <font class="booleano">false</font>');
            break;
        case is_integer($var):
            echo '<font class="tipoPrimario" >(integer)</font> = <font class="numero" >' . ((int) $var) . '</font>';
            break;
        case is_double($var):
            echo '<font class="tipoPrimario" >(double) = <font class="numero" >' . ((double) $var) . '</font>';
            break;
        case is_float($var):
            echo '<font class="tipoPrimario" >(float) = <font class="numero" >' . ((float) $var) . '</font>';
            break;
        case is_string($var):
            echo '<font class="tipoPrimario" >(string) = <font class="string" >"' . ((string) $var) . '"</font>';
            break;
        case is_array($var):
            echo '<table summary="text" border=1 class="array"><tr><td><table class="itens">';
            echo '<tr><td><font class="tipoPrimario" >(array) #' . count($var) . ':</font></td></tr>';
            foreach ($var as $indice => $valor) {
                echo "<tr><td><font class='keyword'>[{$indice}]=></font></td><td>";
                echo debug2($valor, $metodos);
                echo '</td></tr>';
            }
            echo '</tr></table></td></tr></table>';
            break;
        case is_object($var):

            $reflect = new ReflectionObject($var);
            echo '<table summary="text" border=1 class="objeto"><tr><td><table class="propriedades">';
            echo '<tr><td><font class="tipoClasse" title="' . substr($reflect->getDocComment(), 3, -2) . '"><b>(' . $reflect->getName() . ')</b></font></td></tr>';
            if ($metodos) {
                foreach ($reflect->getMethods() as $metodo) {
                    $acesso = null;
                    if ($metodo->isPublic())
                        $acesso = 'public';
                    if ($metodo->isPrivate())
                        $acesso = 'private';
                    if ($metodo->isProtected())
                        $acesso = 'protected';
                    if ($metodo->isStatic())
                        $acesso .= ' static';
                    if ($metodo->isFinal())
                        $acesso .= ' final';
                    $pars = $metodo->getParameters();
                    $comment = explode("\n", substr($metodo->getDocComment(), 3, -2));
                    $commentArgs = array();
                    $commentRet = array('t' => null, 'd' => null);
                    foreach ($comment as $linhaComm) {
                        if (preg_match('/(@\w*)[\ \t\n]*(\w*)[\ \t\n]*(\$\w*){0,1}[\ \t\n]*(.*)/', $linhaComm, $matches)) {
                            switch (strtolower($matches[1])) {
                                case '@param':
                                    $commentArgs[] = array('t' => $matches[2], 'd' => $matches[4]);
                                    break;
                                case '@return':
                                    $commentRet['t'] = $matches[2];
                                    $commentRet['d'] = $matches[3];
                                    break;
                            }
                        }
                    }
                    $args = array();
                    foreach ($pars as $idx => $par) {
                        if (isset($commentArgs[$idx])) {
                            $args[] = $commentArgs[$idx]['t'] . ' <font class="variavel" title="' . $commentArgs[$idx]['d'] . '">$' . $par->getName() . '</font>';
                        } else {
                            $args[] = ' <font class="variavel" >$' . $par->getName() . '</font>';
                        }
                    }
                    $args = implode(', ', $args);
                    switch (strtolower($commentRet['t'])) {
                        case 'boolean':
                            $retorno = '<font class="booleano" >(booleano)</font>';
                            break;
                        case 'integer':
                            $retorno = '<font class="numero" >(integer)</font>';
                            break;
                        case 'double':
                            $retorno = '<font class="numero" >(double)</font>';
                            break;
                        case 'float':
                            $retorno = '<font class="numero" >(float)</font>';
                            break;
                        case 'string':
                            $retorno = '<font class="string" >(string)</font>';
                            break;
                        case 'array':
                            $retorno = '<font class="array" >(array)</font>';
                            break;
                        default:
                            $retorno = ($commentRet['t']) ? '<font class="objeto" >(' . $commentRet['t'] . ')</font>' : '(void)';
                            break;
                    }

                    echo '<tr><td><font class="keyword">' . $acesso . ' </font> ' . $retorno . ' function <font class="metodo" title="' . substr($metodo->getDocComment(), 3, -2) . '">' . $metodo->getName() . '</font>(' . $args . ');</td></tr>';
                }
                return;
            }
            switch (true) {
                case ($var instanceof TData):
                case ($var instanceof TNumerico):
                case ($var instanceof TDocumentoPessoal):
                    echo $var;
                    break;
                case (($var instanceof visualizacao) && !$visualizacao):
                    break;
                default:
                    foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED + ReflectionProperty::IS_PRIVATE) as $prop) {
                        $acesso = null;
                        if ($prop->isPublic())
                            $acesso = 'public';
                        if ($prop->isPrivate())
                            $acesso = 'private';
                        if ($prop->isProtected())
                            $acesso = 'protected';
                        if ($prop->isStatic())
                            $acesso .= ' static';
                        echo '<tr><td><font class="keyword">' . $acesso . ' </font><font class="variavel" title="' . substr($prop->getDocComment(), 3, -2) . '">$' . $prop->getName() . '</font></td><td>';
                        echo debug2(___pegarValorAtributo($var, $prop), $metodos);
                        echo '</td></tr>';
                    }
            }
            echo '</tr></table></td></tr></table>';
            break;
        case is_resource($var):
            echo '<font class="tipoPrimario" >(resource)</font> = ' . $var;
            break;
        case is_null($var):
            echo '<font class="tipoPrimario" > (null)</font> = <font class="nulo" >null</font>';
            break;
        case true:
            echo '<font class="tipoPrimario" >(mixed)</font> = "' . $var . '"';
            break;
    }
}

/**
 * Função para debugar com exibição da classe
 * @param [mixed]
 * @return [string]
 */
function debug3(objeto $var) {
    echo '<link rel="stylesheet" href="'.Productive\Http\Url::root().'/debug.css" />';
    echo '<div class="debug"><pre>';
    ob_start();
    Reflection::export(new ReflectionClass($var));
    $out = ob_get_clean();
    $out = highlight_string("<?php\n" . $out . "?>");
    echo '</div></pre>';
}
function debugCss(){
    echo '<link rel="stylesheet" href="'.Productive\Http\Url::root().'/debug.css" />';
}


function ___pegarValorAtributo($valor, $atributo) {
    try {
        if ($atributo->isProtected() || $atributo->isPrivate()) {
            if ($valor instanceof objeto) {
                if ($atributo->isStatic())
                    throw new Exception();
                return $valor->{'pegar' . ucfirst($atributo->getName())}();
            }
            throw new Exception('');
        }
        if ($atributo->isStatic()) {
            $class = get_class($valor);
            eval("return {$class}::{$atributo->getName()}");
        }
        return $valor->{$atributo->getName()};
    } catch (erro $e) {
        if ($atributo->isStatic())
            return '«««AcessoNegado [Atributo Statico Protegido]»»»';
        return '«««AcessoNegado [Atributo Privado]»»»';
    } catch (Exception $e) {
        return '«««AcessoNegado»»»';
    }
}

/**
 * Função para debugar
 * @param [mixed]
 * @return [string]
 */
function x() {
    $args = func_get_args();
    $ar = debug_backtrace();
    echo "<div class='debug'>Chamada da função x no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
    foreach ($args as $x) {
        echo debug2($x, false, false);
    }
}

function x1($x) {
    $ar = debug_backtrace();
    echo "<div class='debug'>Chamada da função x1 no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
    echo debug1($x);
}

function x2($x, $metodos = false, $visualizacao = false) {
    $ar = debug_backtrace();
    echo "<div class='debug'>Chamada da função x2 no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
    echo debug2($x, $metodos, $visualizacao);
}

function x3($x) {
    $ar = debug_backtrace();
    echo "<div class='debug'>Chamada da função x3 no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
    echo debug3($x);
}

function f($x) {
    $ar = debug_backtrace();
    echo "<div class='debug'>Chamada da função f no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
    echo debug2($x, true);
}

function t(){
    static $timer = false;
    $atual = microtime(true);
    if(!$timer){
        $timer = [
            'inicio' => $atual,
            'anterior' => $atual,
            'parcial' => 0,
            'total' => 0
        ];
    }else{

        $format = function ($duration) {
            $hours = (int) ($duration / 60 / 60);
            $minutes = (int) ($duration / 60) - $hours * 60;
            $seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;
            return ($hours == 0 ? "00" : $hours) . ":" . ($minutes == 0 ? "00" : ($minutes < 10 ? "0" . $minutes : $minutes)) . ":" . ($seconds == 0 ? "00" : ($seconds < 10 ? "0" . $seconds : $seconds));
        };

        $timer['parcial'] = $atual - $timer['anterior'];
        $timer['total'] = $atual - $timer['inicio'];
        $timer['anterior'] = $atual;
        $ar = debug_backtrace();
        $parcial = $format($timer['parcial']);
        $total = $format($timer['total']);
//        x($timer);
        echo <<<time
        <div class='debug'>Chamada da função t no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>
        <font class="booleano" >Total: {$total} Parcial: {$parcial}</font>
time;
    }
}

function errorsOn(){
    ini_set('display_errors','On');
    error_reporting(E_ALL | E_STRICT);
}
