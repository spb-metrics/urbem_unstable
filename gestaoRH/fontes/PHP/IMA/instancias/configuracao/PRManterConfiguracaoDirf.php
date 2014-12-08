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
    * Página de Processamento do Configuração Dirf
    * Data de Criação: 21/11/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.14

    $Id: PRManterConfiguracaoDirf.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirf.class.php"                                );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfPrestador.class.php"                       );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfIrrf.class.php"                            );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfInss.class.php"                            );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfPlano.class.php"                           );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php"                               );
include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                               );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"                            );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDirf";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$arPrestadoresServico          = Sessao::read('arPrestadoresServico');
$arPrestadoresServicoExcluidos = Sessao::read('arPrestadoresServicoExcluidos');
$arPlanoSaude                  = Sessao::read('arPlanoSaude');

$obTIMAConfiguracaoDirf          = new TIMAConfiguracaoDirf();
$obTIMAConfiguracaoDirfPrestador = new TIMAConfiguracaoDirfPrestador();
$obTIMAConfiguracaoDirfIrrf      = new TIMAConfiguracaoDirfIrrf();
$obTIMAConfiguracaoDirfInss      = new TIMAConfiguracaoDirfInss();
$obTIMAConfiguracaoDirfPlano     = new TIMAConfiguracaoDirfPlano();

$obTIMAConfiguracaoDirfPrestador->obTIMAConfiguracaoDirf = &$obTIMAConfiguracaoDirf;

Sessao::setTrataExcecao(true);
switch ($stAcao) {
    case "incluir":
        $obTIMAConfiguracaoDirf->setDado("exercicio",$_POST["inExercicio"]);
        $obTIMAConfiguracaoDirf->recuperaPorChave($rsConfiguracao);
        if ($rsConfiguracao->getNumLinhas() == -1) {
            $obTIMAConfiguracaoDirf->setDado("pagamento_mes_competencia", ($_POST['boPagamentoMes'] == 'Sim' ? 'true' : 'false'));
            $obTIMAConfiguracaoDirf->setDado("cod_natureza",$_POST["inNatureza"]);
            $obTIMAConfiguracaoDirf->setDado("responsavel_prefeitura",$_POST["inCGM"]);
            $obTIMAConfiguracaoDirf->setDado("responsavel_entrega",$_POST["inCGMCNPJ"]);
            $obTIMAConfiguracaoDirf->setDado("telefone",str_replace("-","",$_POST["stTelefone"]));
            $obTIMAConfiguracaoDirf->setDado("ramal",$_POST["stRamal"]);
            $obTIMAConfiguracaoDirf->setDado("fax",str_replace("-","",$_POST["stFax"]));
            $obTIMAConfiguracaoDirf->setDado("email",$_POST["stMail"]);
            $obTIMAConfiguracaoDirf->setDado("cod_evento_molestia", $_POST['inCodigoEventoMolestia']);
            $obTIMAConfiguracaoDirf->inclusao();

            if (is_array($arPrestadoresServico) && count($arPrestadoresServico)>0) {
                foreach ($arPrestadoresServico as $chave => $dadosPrestador) {
                    $obTIMAConfiguracaoDirfPrestador->setDado("exercicio", $_POST["inExercicio"]);
                    $obTIMAConfiguracaoDirfPrestador->setDado("cod_prestador" , "");
                    $obTIMAConfiguracaoDirfPrestador->setDado("cod_dirf" , $dadosPrestador["codigo_retencao"]);
                    $obTIMAConfiguracaoDirfPrestador->setDado("tipo"     , $dadosPrestador["tipo"]);
                    $obTIMAConfiguracaoDirfPrestador->setDado("cod_conta", $dadosPrestador["cod_conta_despesa"]);
                    $obTIMAConfiguracaoDirfPrestador->inclusao();
                }
            }

            if (is_array($arPlanoSaude) &&  count($arPlanoSaude)>0) {
                $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
                foreach ($arPlanoSaude as $key => $arDados) {
                    $obTFolhaPagamentoEvento->setDado('codigo', $arDados['inCodigoEventoPlanoSaude']);
                    $obTFolhaPagamentoEvento->listar($rsEvento);
                    $obTIMAConfiguracaoDirfPlano->setDado('exercicio', $_POST['inExercicio']);
                    $obTIMAConfiguracaoDirfPlano->setDado('numcgm', $arDados['inCGMPlanoSaude']);
                    $obTIMAConfiguracaoDirfPlano->setDado('cod_evento', $rsEvento->getCampo('cod_evento'));
                    $obTIMAConfiguracaoDirfPlano->setDado('registro_ans', $arDados['inRegistro']);
                    $obTIMAConfiguracaoDirfPlano->inclusao();
                }
            }

            if (trim($_POST["inCodClassificacaoIRRF"])!="") {
                $stFiltro  = " WHERE trim(cod_estrutural) = '4.".trim($_POST["inCodClassificacaoIRRF"])."'";
                $stFiltro .= "   AND exercicio = ".$_POST["inExercicio"];
                $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
                $obTContabilidadePlanoConta->recuperaTodos($rsPlanoConta, $stFiltro);

                $obTIMAConfiguracaoDirfIrrf->setDado("exercicio", $_POST["inExercicio"]);
                $obTIMAConfiguracaoDirfIrrf->setDado("cod_conta", $rsPlanoConta->getCampo("cod_conta"));
                $obTIMAConfiguracaoDirfIrrf->inclusao();
            }

            if (trim($_POST["inCodClassificacaoINSS"])!="") {
                $stFiltro  = " WHERE trim(cod_estrutural) = '".trim($_POST["inCodClassificacaoINSS"])."'";
                $stFiltro .= "   AND exercicio = ".$_POST["inExercicio"];
                $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
                $obTContabilidadePlanoConta->recuperaTodos($rsPlanoConta, $stFiltro);

                $obTIMAConfiguracaoDirfInss->setDado("exercicio", $_POST["inExercicio"]);
                $obTIMAConfiguracaoDirfInss->setDado("cod_conta", $rsPlanoConta->getCampo("cod_conta"));
                $obTIMAConfiguracaoDirfInss->inclusao();
            }

            if (is_array($arPlanoSaude) && count($arPlanoSaude)>0) {
            }
        } else {
            $stMensagem = "A configuração da DIRF para o exercício de ".$_POST["inExercicio"]." já foi realizada!";
            Sessao::getExcecao()->setDescricao($stMensagem);
        }
        $pgRetorno = $pgForm;
        $stMensagem = "A configuração da DIRF para o exercício de ".$_POST["inExercicio"]." foi realizada com sucesso!";
        break;
    case "alterar";
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
        $obTFolhaPagamentoEvento->setDado('codigo', $_POST['inCodigoEventoMolestia']);
        $obTFolhaPagamentoEvento->listar($rsEvento);

        $obTIMAConfiguracaoDirf->setDado("exercicio",$_POST["inExercicio"]);
        $obTIMAConfiguracaoDirf->setDado("pagamento_mes_competencia", ($_POST['boPagamentoMes'] == 'Sim' ? 'true' : 'false'));
        $obTIMAConfiguracaoDirf->setDado("cod_natureza",$_POST["inNatureza"]);
        $obTIMAConfiguracaoDirf->setDado("responsavel_prefeitura",$_POST["inCGM"]);
        $obTIMAConfiguracaoDirf->setDado("responsavel_entrega",$_POST["inCGMCNPJ"]);
        $obTIMAConfiguracaoDirf->setDado("telefone",str_replace("-","",$_POST["stTelefone"]));
        $obTIMAConfiguracaoDirf->setDado("ramal",$_POST["stRamal"]);
        $obTIMAConfiguracaoDirf->setDado("fax",str_replace("-","",$_POST["stFax"]));
        $obTIMAConfiguracaoDirf->setDado("email",$_POST["stMail"]);
        $obTIMAConfiguracaoDirf->setDado("cod_evento_molestia", $rsEvento->getCampo('cod_evento'));
        $obTIMAConfiguracaoDirf->alteracao();

        //Excluindo todos os registros para o exercicio
        $stFiltro = " WHERE exercicio = '".$_POST["inExercicio"]."'";
        $obTIMAConfiguracaoDirfPrestador->recuperaTodos($rsConfiguracaoDirfPrestador, $stFiltro);

        while (!$rsConfiguracaoDirfPrestador->eof()) {
            $obTIMAConfiguracaoDirfPrestador->setDado("exercicio"     , $_POST["inExercicio"]);
            $obTIMAConfiguracaoDirfPrestador->setDado("cod_prestador" , $rsConfiguracaoDirfPrestador->getCampo("cod_prestador"));
            $obTIMAConfiguracaoDirfPrestador->exclusao();

            $rsConfiguracaoDirfPrestador->proximo();
        }

        $obTIMAConfiguracaoDirfIrrf->setDado("exercicio", $_POST["inExercicio"]);
        $obTIMAConfiguracaoDirfIrrf->exclusao();

        $obTIMAConfiguracaoDirfInss->setDado("exercicio", $_POST["inExercicio"]);
        $obTIMAConfiguracaoDirfInss->exclusao();

        $obTIMAConfiguracaoDirfPlano->setDado('exercicio', $_POST['inExercicio']);
        $obTIMAConfiguracaoDirfPlano->exclusao();

        $arPlanoSaude = Sessao::read('arPlanoSaude');
        if (is_array($arPlanoSaude) && count($arPlanoSaude)>0) {
               foreach ($arPlanoSaude as $key => $arDados) {
                   $obTFolhaPagamentoEvento->setDado('codigo', $arDados['inCodigoEventoPlanoSaude']);
                   $obTFolhaPagamentoEvento->listar($rsEvento);
                   $obTIMAConfiguracaoDirfPlano->setDado('exercicio', $_POST['inExercicio']);
                   $obTIMAConfiguracaoDirfPlano->setDado('numcgm', $arDados['inCGMPlanoSaude']);
                   $obTIMAConfiguracaoDirfPlano->setDado('cod_evento', $rsEvento->getCampo('cod_evento'));
                   $obTIMAConfiguracaoDirfPlano->setDado('registro_ans', $arDados['inRegistro']);
                   $obTIMAConfiguracaoDirfPlano->inclusao();
               }
        }

        // Inserindo dados alterados
        if (is_array($arPrestadoresServico) && count($arPrestadoresServico)>0) {
            foreach ($arPrestadoresServico as $chave => $dadosPrestador) {
                $obTIMAConfiguracaoDirfPrestador->setDado("exercicio"     , $_POST["inExercicio"]);
                $obTIMAConfiguracaoDirfPrestador->setDado("cod_prestador" , $dadosPrestador["cod_prestador"]);
                $obTIMAConfiguracaoDirfPrestador->setDado("cod_dirf"      , $dadosPrestador["codigo_retencao"]);
                $obTIMAConfiguracaoDirfPrestador->setDado("tipo"          , $dadosPrestador["tipo"]);
                $obTIMAConfiguracaoDirfPrestador->setDado("cod_conta"     , $dadosPrestador["cod_conta_despesa"]);
                $obTIMAConfiguracaoDirfPrestador->inclusao();
            }
        }

        if (trim($_POST["inCodClassificacaoIRRF"])!="") {
            $stFiltro  = " WHERE trim(cod_estrutural) = '4.".trim($_POST["inCodClassificacaoIRRF"])."'";
            $stFiltro .= "   AND exercicio = ".$_POST["inExercicio"];
            $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
            $obTContabilidadePlanoConta->recuperaTodos($rsPlanoConta, $stFiltro);

            $obTIMAConfiguracaoDirfIrrf->setDado("exercicio", $_POST["inExercicio"]);
            $obTIMAConfiguracaoDirfIrrf->setDado("cod_conta", $rsPlanoConta->getCampo("cod_conta"));
            $obTIMAConfiguracaoDirfIrrf->inclusao();
        }

        if (trim($_POST["inCodClassificacaoINSS"])!="") {
            $stFiltro  = " WHERE trim(cod_estrutural) = '".trim($_POST["inCodClassificacaoINSS"])."'";
            $stFiltro .= "   AND exercicio = ".$_POST["inExercicio"];
            $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
            $obTContabilidadePlanoConta->recuperaTodos($rsPlanoConta, $stFiltro);

            $obTIMAConfiguracaoDirfInss->setDado("exercicio", $_POST["inExercicio"]);
            $obTIMAConfiguracaoDirfInss->setDado("cod_conta", $rsPlanoConta->getCampo("cod_conta"));
            $obTIMAConfiguracaoDirfInss->inclusao();
        }

        $pgRetorno = $pgFilt;
        $stMensagem = "A alteração da configuração da DIRF para o exercício de ".$_POST["inExercicio"]." foi realizada com sucesso!";
        break;
    case "excluir":
        $stFiltro = " WHERE exercicio = '".$_GET["inExercicio"]."'";
        $obTIMAConfiguracaoDirfPrestador->recuperaTodos($rsConfiguracaoDirfPrestador, $stFiltro);

        while (!$rsConfiguracaoDirfPrestador->eof()) {
            $obTIMAConfiguracaoDirfPrestador->setDado("exercicio"     , $_POST["inExercicio"]);
            $obTIMAConfiguracaoDirfPrestador->setDado("cod_prestador" , $rsConfiguracaoDirfPrestador->getCampo("cod_prestador"));
            $obTIMAConfiguracaoDirfPrestador->exclusao();

            $rsConfiguracaoDirfPrestador->proximo();
        }

        $obTIMAConfiguracaoDirfIrrf->setDado("exercicio", $_GET["inExercicio"]);
        $obTIMAConfiguracaoDirfIrrf->exclusao();

        $obTIMAConfiguracaoDirfInss->setDado("exercicio", $_GET["inExercicio"]);
        $obTIMAConfiguracaoDirfInss->exclusao();

        $obTIMAConfiguracaoDirfPlano->setDado('exercicio', $_GET['inExercicio']);
        $obTIMAConfiguracaoDirfPlano->exclusao();

        $obTIMAConfiguracaoDirf->setDado("exercicio",$_GET["inExercicio"]);
        $obTIMAConfiguracaoDirf->exclusao();

        $pgRetorno = $pgFilt;
        $stMensagem = "A exclusão da configuração da DIRF para o exercício de ".$_GET["inExercicio"]." foi realizada com sucesso!";
        break;
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgRetorno,$stMensagem,$stAcao,"aviso",Sessao::getId(),"../");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
