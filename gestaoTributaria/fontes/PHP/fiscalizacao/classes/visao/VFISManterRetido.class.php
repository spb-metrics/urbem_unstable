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
    * Classe de Visao do Manter levantamento com retenção na fonte
    * Data de Criação   : 18/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo Vaconcellos de Magalhães

    * @package URBEM
    * @subpackage Visao

*/
require_once( CAM_GT_FIS_NEGOCIO.'RFISManterRetido.class.php' );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once(CAM_GT_FIS_VISAO."VFISManterNota.class.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVencimentoParcela.class.php" );

class VFISManterRetido
{
private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->obVFISManterNota = new VFISManterNota(null);
        $this->obMontaServico = new MontaServico;
    }

    public function incluir($param)
    {
        return $this->controller->incluir($param);
    }

    public function montaListaNotas($rsLista)
    {
        if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsLista );
        $obLista->setTitulo                    ( "Lista de Notas" );
        $obLista->setTotaliza                  ( "flValorRetido,Total Valor Retido,right,8" );

        $obLista->setMostraPaginacao           ( false );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Prestador"            );
        $obLista->ultimoCabecalho->setWidth    ( 25                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Série"                );
        $obLista->ultimoCabecalho->setWidth    ( 8                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Número da Nota"       );
        $obLista->ultimoCabecalho->setWidth    ( 8                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data de Emissão"      );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Alíquotas (%)"        );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Declarado"      );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Retido"         );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "[inCGM] - [stCGM]" );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inSerie"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inNumeroNota"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "dtEmissao"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flAliquota"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorDeclarado"     );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorRetido"        );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirNota();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice1", "inSerie" );
        $obLista->ultimaAcao->addCampo         ( "inIndice2", "inNumeroNota" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
        } else {
        $stHTML = "&nbsp";
        }

        $js = "d.getElementById('spnListaNota').innerHTML = '".$stHTML."';\n";

        return $js;
    }

    public function validaData($_REQUEST)
    {
        return $this->obVFISManterNota->validaData($_REQUEST);

    }

        public function validaExercicio($_REQUEST)
        {
        return $this->obVFISManterNota->validaExercicio($_REQUEST);

    }

    public function alteraCompetencia($_REQUEST)
    {
        return $this->obVFISManterNota->alteraCompetencia($_REQUEST);
    }

    public function PreencheCGM($_REQUEST)
    {
        return $this->obVFISManterNota->PreencheCGM($_REQUEST);
    }

    public function limpaNota($_REQUEST)
    {
        return $this->obVFISManterNota->limpaNota($_REQUEST);
    }

    public function limpaServico($_REQUEST)
    {
        return $this->obVFISManterNota->limpaServico($_REQUEST);
    }

    public function preencheMunicipio($_REQUEST)
    {
        return $this->obVFISManterNota->preencheMunicipio($_REQUEST);
    }

    public function alterarServico($_REQUEST)
    {
         $stServico = $_REQUEST['inIndice1'];
        $arServicoRetencao = Sessao::read( "servicos_retencao" );
        $nregistros = count ( $arServicoRetencao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arServicoRetencao[$inCount]["stServico"] == $stServico) {
                Sessao::write( "servicos_retencao_alterando", $inCount+1 );

                $stJs .= 'f.stChaveServico.value = "'.$arServicoRetencao[$inCount]["stServico"].'";';
                $stJs .= 'f.flAliquota.value = "'.$arServicoRetencao[$inCount]["flAliquota"].'";';
                $stJs .= 'f.flValorDeclarado.value = "'.$arServicoRetencao[$inCount]["flValorDeclarado"].'";';
                $stJs .= 'f.flDeducao.value = "'.$arServicoRetencao[$inCount]["flDeducao"].'";';

                sistemaLegado::executaFrameOculto( $stJs );
            $this->obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
                $this->obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigencia"]   );
                $this->obMontaServico->setCodigoNivelServico   ( $_REQUEST["inCodigoNivel"]      );
                $this->obMontaServico->setValorReduzidoServico ( $arServicoRetencao[$inCount]["stServico"] );

                return $this->obMontaServico->preencheCombos();
                break;
            }
        }

    }

    public function incluirServicoLista($_REQUEST)
    {
        if (!$_REQUEST["stChaveServico"]) {
            $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["flAliquota"]) {
            $stJs = "alertaAviso('@Campo Alíquota inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        } else {
            $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flAliquota"] ) );
            if ($flAliquota <= 0 || $flAliquota > 100) {
                $stJs = "alertaAviso('@Valor da Aliquota inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }
        }

        if (!$_REQUEST["flValorDeclarado"] or $_REQUEST["flValorDeclarado"]=='0,00') {
            $stJs = "alertaAviso('@Campo Valor Declarado inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $arServicoRetencao = Sessao::read( "servicos_retencao" );
        for ( $inX=0; $inX<count ( $arServicoRetencao ); $inX++) {
            if ( Sessao::read( "servicos_retencao_alterando" ) == ($inX+1) )
                continue;

            if ($arServicoRetencao[$inX]["stServico"] == $_REQUEST["stChaveServico"]) {
                $stJs = "alertaAviso('O serviço já está na lista.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.stChaveServico.focus();';
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }
        }

        if ( Sessao::read( "servicos_retencao_alterando" ) ) {
            $inTotalElementos = Sessao::read( "servicos_retencao_alterando" ) - 1;
            Sessao::write( "servicos_retencao_alterando", "" );
            unset( $arServicoRetencao[$inTotalElementos]["flAliquota"] );
            unset( $arServicoRetencao[$inTotalElementos]["flValorDeclarado"] );
            unset( $arServicoRetencao[$inTotalElementos]["flDeducao"] );
            Sessao::write( "servicos_retencao", $arServicoRetencao );
        }else
            $inTotalElementos = count ( $arServicoRetencao );

        $obTCEMServico = new TCEMServico;
        $stFiltro = " WHERE cod_estrutural = '".$_REQUEST["stChaveServico"]."'";
        $obTCEMServico->recuperaTodos( $rsListaServico, $stFiltro );
        if ( $rsListaServico->Eof() ) {
            $stJs = "alertaAviso('Código de serviço inválido (".$_REQUEST["stChaveServico"].").','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.stChaveServico.focus();';
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $arServicoRetencao[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
        $arServicoRetencao[$inTotalElementos]["stServico"] = $_REQUEST["stChaveServico"];
        $arServicoRetencao[$inTotalElementos]["flAliquota"] = $_REQUEST["flAliquota"];
        $arServicoRetencao[$inTotalElementos]["flValorDeclarado"] = $_REQUEST["flValorDeclarado"];
        if ($_REQUEST["flDeducao"])
            $arServicoRetencao[$inTotalElementos]["flDeducao"] = $_REQUEST["flDeducao"];

        $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flValorDeclarado"] ) );
        $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flDeducao"] ) );
        $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flAliquota"] ) );

        $arServicoRetencao[$inTotalElementos]["flValorLancado"] = ( $flValorDeclarado - $flDeducao );
        $arServicoRetencao[$inTotalElementos]["flValorLancado"] = number_format( $arServicoRetencao[$inTotalElementos]["flValorLancado"], 2, ',', '.' );
        Sessao::write( "servicos_retencao", $arServicoRetencao );

        $stJs = 'f.stChaveServico.value = "";';
        $stJs .= 'f.flAliquota.value = "";';
        $stJs .= 'f.flValorDeclarado.value = "";';
        $stJs .= 'f.flDeducao.value = "";';

        $inX = 0;
        while ($_REQUEST) {
            $inX++;
            $stNome = "inCodServico_".$inX;
            if ($_REQUEST[ $stNome ]) {
                $stJs .= 'f.'.$stNome.'.value = "";';
            }else
                break;
        }

        $rsListaServicos = new RecordSet;
        $rsListaServicos->preenche ( $arServicoRetencao );

        $stJs .= $this->obVFISManterNota->montaListaServicos ( $rsListaServicos );

                $obFormulario = new Formulario;
                //botoes Nota
                $obBtnIncluirNota = new Button;
                $obBtnIncluirNota->setName              ( "btnIncluirNota" );
                $obBtnIncluirNota->setValue             ( "Incluir" );
                $obBtnIncluirNota->setTipo              ( "button" );
                $obBtnIncluirNota->obEvento->setOnClick ( "incluirNotaLista();" );
                $obBtnIncluirNota->setDisabled          ( false );

                $botoesNota = array ( $obBtnIncluirNota );
                $obFormulario->defineBarra ( $botoesNota, 'left', '' );
                $obFormulario->montaInnerHTML();
                $stJs .= "d.getElementById('botaoNota').innerHTML = '". $obFormulario->getHTML(). "';\n";

        return sistemaLegado::executaFrameOculto( $stJs );
    }

    public function excluirNota($_REQUEST)
    {
        $inSerie = $_REQUEST['inIndice1'];
            $inNumeroNota = $_REQUEST['inIndice2'];

            $arTmpServico = array();
            $inCountArray = 0;
        $arNotasRetencao = Sessao::read( "notas_retencao" );
        $nregistros = count ( $arNotasRetencao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arNotasRetencao[$inCount]["inNumeroNota"] != $inNumeroNota ||
                $arNotasRetencao[$inCount]["inSerie"] != $inSerie) {
                $arTmpServico[$inCountArray] = $arNotasRetencao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( "notas_retencao", $arTmpServico );

        $rsListaNotas = new RecordSet;
        $rsListaNotas->preenche ( $arTmpServico );

        $stJs = $this->montaListaNotas ( $rsListaNotas );

        return sistemaLegado::executaFrameOculto( $stJs );
    }

    public function excluirServico($_REQUEST)
    {
        $stServico = $_REQUEST['inIndice1'];

        $arTmpServico = array();
        $inCountArray = 0;

        $arServicoRetencao = Sessao::read( "servicos_retencao" );
        $nregistros = count ( $arServicoRetencao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arServicoRetencao[$inCount]["stServico"] != $stServico) {
                $arTmpServico[$inCountArray] = $arServicoRetencao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( "servicos_retencao", $arTmpServico );

        $rsListaServicos = new RecordSet;
        $rsListaServicos->preenche ( $arTmpServico );

        $stJs = $this->obVFISManterNota->montaListaServicos ( $rsListaServicos );

        return sistemaLegado::executaFrameOculto( $stJs );

    }

    public function incluirNotaLista($_REQUEST)
    {
        if (!$_REQUEST["inCGM"]) {
            $stJs = "alertaAviso('@Campo CGM do Prestador inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inCodigoUF"]) {
            $stJs = "alertaAviso('@Campo Estado inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inCodigoMunicipio"]) {
            $stJs = "alertaAviso('@Campo Município inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["dtEmissao"]) {
            $stJs = "alertaAviso('@Campo Data da Emissão vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inNumeroNota"]) {
            $stJs = "alertaAviso('@Campo Número da Nota vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inSerie"]) {
            $stJs = "alertaAviso('@Campo Série vazio.','f$this->obVFISManterNota->montaListaServicosorm','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $nregistros = count ( Sessao::read('servicos_retencao') );
        if ($nregistros <= 0) {
            $stJs = "alertaAviso('@Lista de Serviços vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $arNotasRetencao = Sessao::read("notas_retencao");
        $nroNotas = count ( $arNotasRetencao );

        $boIncluir = true;
        for ($inX=0; $inX<$nroNotas; $inX++) {
            if ($arNotasRetencao[$inX]["inNumeroNota"] == $_REQUEST["inNumeroNota"]
                && $arNotasRetencao[$inX]["inSerie"] == $_REQUEST["inSerie"]) {
                $stJs = "alertaAviso('@A nota já está na lista.','form','erro','".Sessao::getId()."');";
                $boIncluir = false;
                break;
            }
        }

        if ($boIncluir) {
            $flTotalRetido = 0;
            $flTotalDeclarado = 0;
            $flTotalDeducao = 0;
            $stAliquota = "";
            $arServicoRetencao = Sessao::read( "servicos_retencao");
            for ($inX=0; $inX<$nregistros; $inX++) {
                $flDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorDeclarado"] ) );
                $flTotalDeclarado += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorDeclarado"] ) );
                $flTotalRetido += ( $flDeclarado - $arServicoRetencao[$inX]["flDeducao"] );
                $flTotalDeducao += $arServicoRetencao[$inX]["flDeducao"];
                if ($arServicoRetencao[$inX]["flAliquota"]) {
                    $stAliquota .= $arServicoRetencao[$inX]["flAliquota"];
                    if ( $nregistros != 1)
                        $stAliquota .= ";";
                }
            }

            $rsListaServicos = new RecordSet;
            $rsListaNotas = new RecordSet;

            $obTCGM = new TCGM;
            $obTCGM->setDado( "numcgm", $_REQUEST["inCGM"] );
            $obTCGM->recuperaPorChave( $rsCGM );
            if ( !$rsCGM->Eof() ) {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $arNotasRetencao[$nroNotas]["stCGM"] = $stNomCgm;
            }

            $arNotasRetencao[$nroNotas]["inCGM"] = $_REQUEST["inCGM"];
            $arNotasRetencao[$nroNotas]["stEstado"] = $_REQUEST["inCodigoUF"];
            $arNotasRetencao[$nroNotas]["stMunicipio"] = $_REQUEST["inCodigoMunicipio"];
            $arNotasRetencao[$nroNotas]["inNumeroNota"] = $_REQUEST["inNumeroNota"];
            $arNotasRetencao[$nroNotas]["inSerie"] = $_REQUEST["inSerie"];
            $arNotasRetencao[$nroNotas]["dtEmissao"] = $_REQUEST["dtEmissao"];
            $arNotasRetencao[$nroNotas]["flValorDeclarado"] = number_format( $flTotalDeclarado, 2, ',', '.' );
            $arNotasRetencao[$nroNotas]["flValorRetido"] = number_format( $flTotalRetido, 2, ',', '.' );

            $arNotasRetencao[$nroNotas]["flValorDeclaradoEUA"] = number_format( $flTotalDeclarado, 2, '.', '' );
            $arNotasRetencao[$nroNotas]["flValorRetidoEUA"] = number_format( $flTotalRetido, 2, '.', '' );
            $arNotasRetencao[$nroNotas]["flValorDeducaoEUA"] = number_format( $flTotalDeducao, 2, '.', '' );

            $arNotasRetencao[$nroNotas]["flAliquota"] = $stAliquota;
            $arNotasRetencao[$nroNotas]["arServicos"] = Sessao::read( 'servicos_retencao' );

            Sessao::write( 'servicos_retencao', array() );
            Sessao::write( 'notas_retencao', $arNotasRetencao );

            $rsListaServicos->preenche ( array() );
            $rsListaNotas->preenche ( $arNotasRetencao );

            $stJs = $this->obVFISManterNota->montaListaServicos ( $rsListaServicos );
            $stJs .= $this->montaListaNotas ( $rsListaNotas );
            $stJs .= 'f.inNumeroNota.value = "";';
            $stJs .= 'f.inSerie.value = "";';
            $stJs .= 'f.dtEmissao.value = "";';
            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodUF.value = "";';
            $stJs .= 'f.inCGM.value = "";';
            $stJs .= "d.getElementById('stCGM').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'f.inCodMunicipio.value = "";';
        }

        return sistemaLegado::executaFrameOculto( $stJs );
    }

    public function preencheProxComboServico($_REQUEST)
    {
        return $this->obVFISManterNota->preencheProxComboServico($_REQUEST);
    }
    public function preencheCombosServico($_REQUEST)
    {
        return $this->obVFISManterNota->preencheCombosServico($_REQUEST);
    }

}
