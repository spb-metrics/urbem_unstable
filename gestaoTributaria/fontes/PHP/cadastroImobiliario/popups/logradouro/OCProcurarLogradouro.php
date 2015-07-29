<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de processamento oculto para o cadastro de logradouro
    * Data de Criação   : 08/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
                             Gustavo Passos Tourinho
                             Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: OCProcurarLogradouro.php 62960 2015-07-13 14:00:58Z evandro $

    * Casos de uso: uc-05.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"  );
include_once CAM_GT_CIM_NEGOCIO.'RCIMLogradouro.class.php';

// Guarda a ação antiga para ser escrita ao final do script.
$acao   = Sessao::read('acao');
$modulo = Sessao::read('modulo');

//Define o nome dos arquivos PHP
$stPrograma          = "ProcurarLogradouro";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgFormVerificaNivel = "FM".$stPrograma."VerificaNivel.php";
$pgFormNivel         = "FM".$stPrograma."Nivel.php";
$pgFormUltimoNivel   = "FM".$stPrograma."UltimoNivel.php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgJs                = "JS".$stPrograma.".js";
$pgBairro            = "../bairro/FMManterBairro.php";

include_once( $pgJs );

// INSTANCIA OBJETO
$obRCIMBairro = new RCIMBairro;

// FUNCOES PARA MONTAR LISTAS
function montaListaBairro($arListaBairros, $boRetorna = false)
{
    if ( count( $arListaBairros ) ) {

        $rsListarBairros = new RecordSet;
        $rsListarBairros->preenche ( $arListaBairros   );

        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListarBairros   );
        $obLista->setTitulo                    ( "Lista de Bairros" );
        $obLista->setMostraPaginacao           ( false              );
        $obLista->addCabecalho                 (                    );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"           );
        $obLista->ultimoCabecalho->setWidth    ( 2                  );
        $obLista->commitCabecalho              (                    );
        $obLista->addCabecalho                 (                    );
        $obLista->ultimoCabecalho->addConteudo ( "Código do Bairro" );
        $obLista->ultimoCabecalho->setWidth    ( 10                 );
        $obLista->commitCabecalho              (                    );
        $obLista->addCabecalho                 (                    );
        $obLista->ultimoCabecalho->addConteudo ( "Nome do Bairro"   );
        $obLista->ultimoCabecalho->setWidth    ( 40                 );
        $obLista->commitCabecalho              (                    );
        $obLista->addCabecalho                 (                    );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"           );
        $obLista->ultimoCabecalho->setWidth    ( 2                  );
        $obLista->commitCabecalho              (                    );

        $obLista->addDado                      (                    );
        $obLista->ultimoDado->setCampo         ( "cod_bairro"       );
        $obLista->ultimoDado->setAlinhamento   ( "DIREITA"          );
        $obLista->commitDado                   (                    );
        $obLista->addDado                      (                    );
        $obLista->ultimoDado->setCampo         ( "nom_bairro"       );
        $obLista->commitDado                   (                    );

        $obLista->addAcao                      (                    );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"          );
        $obLista->ultimaAcao->setFuncao        ( true               );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirBairro();" );
        $obLista->ultimaAcao->addCampo         ("1","cod_bairro"    );
        $obLista->commitAcao                   (                    );

        $obLista->montaHTML                    (                    );
        $stHTML =  $obLista->getHtml           (                    );
        $stHTML = str_replace                  ("\n","",$stHTML     );
        $stHTML = str_replace                  ("  ","",$stHTML     );
        $stHTML = str_replace                  ("'","\\'",$stHTML   );

    } else {

        $stHTML = "&nbsp";

    }

    $js .= "d.getElementById('spanListarBairro').innerHTML = '".$stHTML."';\n";
    if ($boRetorna) {
        return $js;
    } else {
        sistemaLegado::executaIFrameOculto($js);
    }
}

function montaListaCEP($arListaCEP, $boRetorna = false)
{
    if ( count( $arListaCEP ) ) {

        $rsListarCEP = new RecordSet;
        $rsListarCEP->preenche ( $arListaCEP     );

        function corrige_cep($valor)
        {
            if ( strlen($valor["cep"]) == 8 && is_int(strlen($valor["cep"]))) {
                $valor["cep"] = substr($valor["cep"],0,5).'-'.substr($valor["cep"],5,3);
            }
            if ( strlen($valor["num_inicial"])<1 )
                $valor["num_inicial"] = " &nbsp; ";
            if ( strlen($valor["num_final"])<1 )
                $valor["num_final"] = " &nbsp; ";
            if ( strlen($valor["numeracao"])<1 )
                $valor["numeracao"] = " &nbsp; ";

            return $valor;

        }
        $rsListarCEP->arElementos = array_map("corrige_cep",$rsListarCEP->arElementos);

        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListarCEP     );
        $obLista->setTitulo                    ( "Lista de CEP's" );
        $obLista->setMostraPaginacao           ( false            );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
        $obLista->ultimoCabecalho->setWidth    ( 2                );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "CEP"            );
        $obLista->ultimoCabecalho->setWidth    ( 15               );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "N&uacute;mero Inicial" );
        $obLista->ultimoCabecalho->setWidth    ( 15               );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "N&uacute;mero Final"   );
        $obLista->ultimoCabecalho->setWidth    ( 15               );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "Numera&ccedil;&atilde;o"      );
        $obLista->ultimoCabecalho->setWidth    ( 15               );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
        $obLista->ultimoCabecalho->setWidth    ( 2                );
        $obLista->commitCabecalho              (                  );

        $obLista->addDado                      (                  );
        $obLista->ultimoDado->setCampo         ( "cep"            );
        $obLista->commitDado                   (                  );
        $obLista->addDado                      (                  );
        $obLista->ultimoDado->setCampo         ( "num_inicial"    );
        $obLista->commitDado                   (                  );
        $obLista->addDado                      (                  );
        $obLista->ultimoDado->setCampo         ( "num_final"      );
        $obLista->commitDado                   (                  );
        $obLista->addDado                      (                  );
        $obLista->ultimoDado->setCampo         ( "numeracao"      );
        $obLista->commitDado                   (                  );

        $obLista->addAcao                      (                  );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"        );
        $obLista->ultimaAcao->setFuncao        ( true             );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirCEP();" );
        $obLista->ultimaAcao->addCampo         ( "1","cep"        );
        $obLista->commitAcao                   (                  );

        $obLista->montaHTML                    (                  );
        $stHTML = $obLista->getHtml            (                  );
        $stHTML = str_replace                  ( "\n","",$stHTML  );
        $stHTML = str_replace                  ( "  ","",$stHTML  );
        $stHTML = str_replace                  ( "'","\\'",$stHTML);
    } else {
        $stHTML = "&nbsp";
    }

    $js .= "d.getElementById('spanListarCEP').innerHTML = '".$stHTML."';\n";
    $js .= "f.inCEP.value=''; \n";
    $js .= "f.inInicial.value=''; \n";
    $js .= "f.inFinal.value=''; \n";
    $js .= "f.boNumeracao[0].checked = true; \n";
    if ($boRetorna) {
        return $js;
    } else {
        sistemaLegado::executaIFrameOculto($js);
    }
}

function montaListaHistorico()
{
    
    $obRCIMLogradouro = new RCIMLogradouro;    
    
    $obRCIMLogradouro->setCodigoUF( $_REQUEST["inCodUF"] );
    $obRCIMLogradouro->setCodigoMunicipio( $_REQUEST["inCodMunicipio"] );
    $obRCIMLogradouro->setCodigoLogradouro( $_REQUEST["inCodigoLogradouro"] );

    $obRCIMLogradouro->listarHistoricoLogradouros( $rsLista, $boTransacao, "" );

    if ($rsLista->getNumLinhas() > 0) {
        
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsLista     );
        $obLista->setTitulo                    ( "Histórico do Logradouro" );
        $obLista->setMostraPaginacao           ( false            );
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Código ");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome do Logradouro" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome do Bairro" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Município" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "CEP" );
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data Logradouro" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();


        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "cod_logradouro" );
        $obLista->commitDado();

        $obLista->addDado();        
        $obLista->ultimoDado->setCampo( "tipo_nome" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "nom_bairro" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[sigla_uf] - [nom_municipio]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "cep" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "data_logradouro" );
        $obLista->commitDado();

        $obLista->montaHTML                    (                  );
        $stHTML = $obLista->getHtml            (                  );
        $stHTML = str_replace                  ( "\n","",$stHTML  );
        $stHTML = str_replace                  ( "  ","",$stHTML  );
        $stHTML = str_replace                  ( "'","\\'",$stHTML);
        
        $js .= "d.getElementById('spanListarHistorico').innerHTML = '".$stHTML."';\n";
    
        return $js;
    }    
    
}


// SELECIONA ACAO
switch ($_REQUEST ["stCtrl"]) {
    case "incluirNovoBairro":

        Sessao::write('acao'  ,"784");
        Sessao::write('modulo',  "0");

        if (!$_REQUEST[ "stNovoBairro" ]) {
            $js= " alertaAviso('Campo \'Novo Bairro\' vazio.','form','erro','".Sessao::getId()."', '../');\n";
            sistemaLegado::executaIFrameOculto($js);
            exit;
        }

        $obRCIMBairro->setNomeBairro      ( $_REQUEST[ "stNovoBairro" ] );
        $obRCIMBairro->setCodigoUF        ( $_REQUEST[ "inCodUF" ] );
        $obRCIMBairro->setCodigoMunicipio ( $_REQUEST[ "inCodMunicipio" ] );

        $obRCIMBairro->incluirBairro();

        $js .= "f.stNovoBairro.value=''; \n";
        $js .= "f.inCodigoBairro.value=''; \n";
        $js .= "limpaSelect(f.inCodBairro,0); \n";
        $js .= "f.inCodBairro[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodMunicipio"]) {
            unset( $obRCIMBairro );
            $obRCIMBairro = new RCIMBairro;
            $obRCIMBairro->setCodigoMunicipio( $_REQUEST["inCodMunicipio"] );
            $obRCIMBairro->setCodigoUF( $_REQUEST["inCodUF"] );
            $obRCIMBairro->listarBairros ( $rsBairros );

            $inContador = 1;
        } else {
            $rsBairros = new RecordSet;
        }
        while ( !$rsBairros->eof() ) {
            $inCodBairro = $rsBairros->getCampo( "cod_bairro" );
            $stNomBairro = $rsBairros->getCampo( "nom_bairro" );
            $js .= "f.inCodBairro.options[$inContador] = new Option('".addslashes($stNomBairro)."','".$inCodBairro."'); \n";
            $inContador++;
            $rsBairros->proximo();
        }

        sistemaLegado::executaIFrameOculto($js);
        break;

    case "preencheMunicipio":
        $js .= "f.inCodigoBairro.value=''; \n";
        $js .= "limpaSelect(f.inCodBairro,0); \n";
        $js .= "f.inCodBairro[0] = new Option('Selecione','', 'selected');\n";

        $js .= "f.inCodigoMunicipio.value=''; \n";
        $js .= "limpaSelect(f.inCodMunicipio,0); \n";
        $js .= "f.inCodMunicipio[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodigoUF"]) {
            $obRCIMBairro->setCodigoUF( $_REQUEST["inCodigoUF"] );
            $obRCIMBairro->listarMunicipios( $rsMunicipios );

            $inContador = 1;
            while ( !$rsMunicipios->eof() ) {
                $inCodMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
                $stNomMunicipio = $rsMunicipios->getCampo( "nom_municipio" );
                $js .= "f.inCodMunicipio.options[$inContador] = new Option('".addslashes($stNomMunicipio)."','".$inCodMunicipio."'); \n";
                $inContador++;
                $rsMunicipios->proximo();
            }
        }

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inCodigoMunicipio.value='".$_REQUEST["inCodigoMunicipio"]."'; \n";
            $js .= "f.inCodMunicipio.options[".$_REQUEST["inCodigoMunicipio"]."].selected = true; \n";
        }
        sistemaLegado::executaIFrameOculto($js);
    break;

    case "preencheBairro":
        $js .= "f.inCodigoBairro.value=''; \n";
        $js .= "limpaSelect(f.inCodBairro,0); \n";
        $js .= "f.inCodBairro[0] = new Option('Selecione','', 'selected');\n";
        if ($_POST["inCodMunicipio"]) {
            $obRCIMBairro->setCodigoMunicipio( $_REQUEST["inCodMunicipio"] );
            $obRCIMBairro->setCodigoUF( $_REQUEST["inCodUF"] );
            $obRCIMBairro->listarBairros ( $rsBairros );
            $inContador = 1;
        } else {
            $rsBairros = new RecordSet;
        }
        while ( !$rsBairros->eof() ) {
            $inCodBairro = $rsBairros->getCampo( "cod_bairro" );
            $stNomBairro = $rsBairros->getCampo( "nom_bairro" );
            $js .= "f.inCodBairro.options[$inContador] = new Option('".addslashes($stNomBairro)."','".$inCodBairro."'); \n";
            $inContador++;
            $rsBairros->proximo();
        }

        sistemaLegado::executaIFrameOculto($js);
    break;

    case "incluirBairro":

        $obRCIMBairro = new RCIMBairro;
        $arBairros = $arTmpBairro = array ();

        $inCodigoMunicipio = $_REQUEST["inCodigoMunicipio"] ? $_REQUEST["inCodigoMunicipio"] : $sessao["cod_municipio"];
        $inCodigoUF = $_REQUEST["inCodigoUF"] ? $_REQUEST["inCodigoUF"] : $sessao["cod_uf"];

        $obRCIMBairro->setCodigoBairro    ( $_REQUEST["inCodigoBairro"] );
        $obRCIMBairro->setCodigoMunicipio ( $inCodigoMunicipio );
        $obRCIMBairro->setCodigoUF        ( $inCodigoUF );
        $obErro = $obRCIMBairro->consultarBairro();

        if ( !$obErro->ocorreu() ) {
            $arBairros["nom_bairro"] = $obRCIMBairro->getNomeBairro();
            $arBairros["cod_bairro"] = $_REQUEST["inCodigoBairro"];

            $stInsere = false;
            $arBairrosSessao = Sessao::read('bairros');
            if ($arBairrosSessao) {
                $inCountSessao = count ($arBairrosSessao);
            } else {
                $inCountSessao = 0;
                $stInsere = true;
            }

            for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                if ($arBairrosSessao[$iCount]["cod_bairro"] == $arBairros["cod_bairro"]) {
                    $stInsere = false;
                    $iCount = $inCountSessao;
                } else {
                    $stInsere = true;
                }
            }
            if ($stInsere) {
                if ($arBairrosSessao) {
                    $inLast = count ($arBairrosSessao);
                } else {
                    $inLast = 0;
                    $arBairrosSessao = array ();
                    Sessao::write('bairros', $arBairrosSessao);
                }
                $arBairrosSessao[$inLast]["cod_bairro"] = $arBairros["cod_bairro"];
                $arBairrosSessao[$inLast]["nom_bairro"] = $arBairros["nom_bairro"];
                Sessao::write('bairros', $arBairrosSessao);
                montaListaBairro ( $arBairrosSessao );
            } else {
                $js = " mensagem += \"@Bairro já informado! (".$obRCIMBairro->getNomeBairro().")\";\n";
                $js.= " alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";
                sistemaLegado::executaIFrameOculto($js);
            }
        } else {
            $js = " mensagem += \"@".$obErro->getDescricao()."!\";\n";
            $js.= " alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";
            sistemaLegado::executaIFrameOculto($js);
        }
    break;

    case "excluirBairro":

        $arTmpBairro = array ();
        $inCountArray = 0;
        $arBairrosSessao = Sessao::read('bairros');
        $inCountSessao = count ( $arBairrosSessao );

        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {

            if ($arBairrosSessao[$inCount][ "cod_bairro" ] == $_REQUEST[ "inIndice" ]) {

                //VERIFICA SE O BAIRRO ESTA VINCULADO A ALGUM REGISTRO DOMICILIO INFORMADO
                $inCodBairroAtual       = $arBairrosSessao[$inCount][ "cod_bairro" ];
                $inCodLogradouroAtual   = $arBairrosSessao[$inCount][ "cod_logradouro" ];
                $inCodMunicipioAtual    = $arBairrosSessao[$inCount][ "cod_municipio" ];
                $inCodUFAtual           = $arBairrosSessao[$inCount][ "cod_uf" ];

                include_once( CAM_GT_CEM_MAPEAMENTO."TCEMDomicilioInformado.class.php" );
                $obTCEMDomicilioInformado = new TCEMDomicilioInformado;

                $stFiltro = " cod_logradouro = ". $inCodLogradouroAtual." AND\n";
                $stFiltro .=" cod_bairro = ". $inCodBairroAtual." AND\n";
                $stFiltro .=" cod_municipio = ". $inCodMunicipioAtual." AND\n";
                $stFiltro .=" cod_uf = ". $inCodUFAtual." \n";

                $stFiltro = " WHERE ".$stFiltro;
                $stOrdem = " ";
                $obTCEMDomicilioInformado->recuperaTodos ( $rsRegistos, $stFiltro, $stOrdem, $boTransacao );

                if ( $rsRegistos->getNumLinhas() > 0 ) {
                    $mensagem = "Bairro utilizado por Inscrição Econômica em seu endereço de <b>DOMICÍLIO FISCAL</b>";
                    $js.= " alertaAviso('". $mensagem ."','form','erro','".Sessao::getId()."', '../'); \n";
                    sistemaLegado::executaIFrameOculto($js);
                }
            }
        }

        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {

            if ($arBairrosSessao[$inCount][ "cod_bairro" ] != $_REQUEST[ "inIndice" ]) {

                $arTmpBairro[$inCountArray]["cod_bairro"] = $arBairrosSessao[$inCount][ "cod_bairro" ];
                $arTmpBairro[$inCountArray]["nom_bairro"] = $arBairrosSessao[$inCount][ "nom_bairro" ];
                $inCountArray++;

            }

        }
        $arBairrosSessao = array();
        $arBairrosSessao = $arTmpBairro;
        Sessao::write('bairros', $arBairrosSessao);

        montaListaBairro ( $arBairrosSessao );

    break;

    case "incluirCEP":
        $inCEP = explode ("-", $_REQUEST[ "inCEP" ]);
        $inCEP = $inCEP[0].$inCEP[1];

        $arCEP = $arTmpBairro = array ();
        $arCEP[ "cep"         ] = $inCEP;
        $arCEP[ "num_inicial" ] = $_REQUEST[ "inInicial"   ];
        $arCEP[ "num_final"   ] = $_REQUEST[ "inFinal"     ];
        if ($_REQUEST[ "boNumeracao" ] == "Pares") {
            $arCEP[ "par"       ] = "true";
            $arCEP[ "impar"     ] = "false";
            $arCEP[ "numeracao" ] = "Pares";
        } elseif ($_REQUEST["boNumeracao"] == "Ímpares") {
            $arCEP[ "impar"     ] = "true";
            $arCEP[ "par"       ] = "false";
            $arCEP[ "numeracao" ] = "&Iacute;mpares";
        } else {
            $arCEP[ "impar"     ] = "true";
            $arCEP[ "par"       ] = "true";
            $arCEP[ "numeracao" ] = "Todos";
        }

        $stInsere = false;
        $arCepSessao = Sessao::read('cep');
        if ($arCepSessao) {
            $inCountSessao = count ($arCepSessao);
        } else {
            $inCountSessao = 0;
            $stInsere = true;
        }

        for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
            if ($arCepSessao[$iCount]["cep"] == $arCEP["cep"]) {
                $stInsere = false;
                $iCount = $inCountSessao;
            } else {
                $stInsere = true;
            }
        }
        if ($stInsere) {
            if ($arCepSessao) {
                $inLast = count ($arCepSessao);
            } else {
                $inLast = 0;
                $arCepSessao = array ();
                Sessao::write('cep', $arCepSessao);
            }
            $arCepSessao[$inLast]["cep"        ] = $arCEP["cep"        ];
            $arCepSessao[$inLast]["num_inicial"] = $arCEP["num_inicial"];
            $arCepSessao[$inLast]["num_final"  ] = $arCEP["num_final"  ];
            $arCepSessao[$inLast]["par"        ] = $arCEP["par"        ];
            $arCepSessao[$inLast]["impar"      ] = $arCEP["impar"      ];
            $arCepSessao[$inLast]["numeracao"  ] = $arCEP["numeracao"  ];
            Sessao::write('cep', $arCepSessao);

            montaListaCEP ( $arCepSessao );
            exit (0);
        } else {
            $js = " mensagem += \"@CEP já informado! (".$_REQUEST[ "inCEP" ].")\";\n";
            $js .= "f.inCEP.value=''; \n";
            $js .= "f.inInicial.value=''; \n";
            $js .= "f.inFinal.value=''; \n";
            $js .= "f.boNumeracao[0].checked = true; \n";
            $js.= " alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";
            sistemaLegado::executaIFrameOculto($js);
            exit(0);
        }
    break;

    case "excluirCEP":
        $arTmpCEP = array ();
        $arCepSessao = Sessao::read('cep');
        $inCountSessao = count ($arCepSessao);
        $inCountArray = 0;

        $obRCIMLogradouro = new RCIMLogradouro;
        $inCodLogradouro = $request->get('inCodigoLogradouro');
        $inCEP = str_replace("-", "", $request->get('inIndice'));

        $stFiltro  = " WHERE cod_logradouro = ".$inCodLogradouro;
        $stFiltro .= " AND cep = '".$inCEP."'";
        $obRCIMLogradouro->obTCEPLogradouro->recuperaRelacionamentoCGMLogradouro($rsCGMLogradouro, $stFiltro, "", $boTransacao);
        
        if ( $rsCGMLogradouro->getNumLinhas() > 0 ) {
            $mensagem = "Exclusão não permitida pois o CEP está sendo utilizado.";
            $js.= " alertaAviso('". $mensagem ."','form','erro','".Sessao::getId()."', '../'); \n";
            sistemaLegado::executaIFrameOculto($js);
            exit();
        }else{
            $obRCIMLogradouro->excluirCEPLogradouro($inCEP,$boTransacao);
            for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
                //if ($sessao->transf6[ "cep" ][$inCount][ "cep" ] != $_REQUEST[ "inIndice" ])
                $cepSessao = substr($_REQUEST[ "inIndice" ],0,5).substr($_REQUEST[ "inIndice" ],6,3);
                if ($arCepSessao[$inCount][ "cep" ] != $cepSessao) {
                    $arTmpCEP[$inCountArray]["cep"]         = $arCepSessao[$inCount][ "cep" ];
                    $arTmpCEP[$inCountArray]["num_inicial"] = $arCepSessao[$inCount][ "num_inicial" ];
                    $arTmpCEP[$inCountArray]["num_final"]   = $arCepSessao[$inCount][ "num_final" ];
                    $arTmpCEP[$inCountArray]["numeracao"]   = $arCepSessao[$inCount][ "numeracao" ];
                    // Esperando campo na tabela de CEP_LOGRADOURO
                    $inCountArray++;
                }
            }
        }
        
        $arCepSessao = array();
        $arCepSessao = $arTmpCEP;
        
        Sessao::write('cep', $arCepSessao);

        montaListaCEP ( $arCepSessao );
    break;

    case 'limparListas' :
        $stJs .= "f.inCodigoTipo.value = '';\n";
        $stJs .= "f.inCodTipo.options[0].selected = true;\n";
        $stJs .= "f.stNomeLogradouro.value = '';\n";
        $stJs .= "f.inCodigoBairro.value = '';\n";
        $stJs .= "f.inCodBairro.options[0].selected = true;\n";
        $stJs .= montaListaBairro ( Sessao::write('bairros', array()), true);
        $stJs .= montaListaCEP ( Sessao::write('cep', array()) , true);
        SistemaLegado::executaIFrameOculto($stJs);
    break;

    case 'preencheInner':
        $arBairrosSessao = Sessao::read('bairros');
        $arCepSessao     = Sessao::read('cep');
        if ($arBairrosSessao) {
            $stJs = montaListaBairro ( $arBairrosSessao , true);
        }
        if ($arCepSessao) {
            $stJs .= montaListaCEP ( $arCepSessao, true);
        }

        $stJs .= montaListaHistorico();

        SistemaLegado::executaIFrameOculto($stJs);
    break;

    case 'IniciaSessions':

        $arBairrosSessao = array();
        $arCepSessao     = array();
        Sessao::write('bairros', $arBairrosSessao);
        Sessao::write('cep'    , $arCepSessao);

        $stJs = montaListaBairro    ( $arBairrosSessao , true);
        $stJs .= montaListaCEP      ( $arCepSessao     , true);

        SistemaLegado::executaIFrameOculto($stJs);
    break;

    case 'verificaCodigoLogradouro':
        $inCodLogradouro = $_REQUEST['inCodLogradouro'];

        if (empty($inCodLogradouro)) {
            $stJs .= "window.parent.document.frm.submit();";
            SistemaLegado::executaIFrameOculto($stJs);
            break;
        }

        $obRCIMLogradouro = new RCIMLogradouro;
        $obRCIMLogradouro->setCodigoLogradouro($inCodLogradouro);
        $obRCIMLogradouro->consultarLogradouro($rsLogradouro);

        if ($rsLogradouro->inNumLinhas > 0) {
            $obTLogradouro= new TLogradouro();
            $obTLogradouro->proximoCod($inProxCodLogradouro);

            $stJs .= "if (confirm('O Código ".$inCodLogradouro." já foi utilizado. Deseja utilizar próximo código: ".$inProxCodLogradouro."')) { window.parent.document.frm.submit(); } else { false; };";
        } else {
            $stJs .= "window.parent.document.frm.submit();";
        }

        SistemaLegado::executaIFrameOculto($stJs);
    break;
}

// Escreve na sessão a última ação responsável por chamar a pop-up de logradouro.
Sessao::write('acao', $acao);
Sessao::write('modulo', $modulo);

?>
