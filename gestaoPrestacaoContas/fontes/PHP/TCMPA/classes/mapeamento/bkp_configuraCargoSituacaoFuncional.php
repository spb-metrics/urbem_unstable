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
    * Página de Processamento - configurações do Arquivo TCMPA
    * Data de Criação   : 02/06/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPALotacao.class.php";
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPAConfiguraTipoCargo.class.php";
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPAConfiguraSituacaoFuncional.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterCargoSituacaoFuncional";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$sessao = $_SESSION['sessao'];

$obTTPALotacao = new TTPALotacao();
$obTTPAConfiguraTipoCargo = new TTPAConfiguraTipoCargo();
$obTTPAConfiguraSituacaoFuncional = new TTPAConfiguraSituacaoFuncional();

$obTTPALotacao->setDado( 'cod_sub_divisao', $sessao->transf['lotacao'][0]['inCodSubDivisao']);
$obTTPALotacao->setDado( 'cod_regime', $sessao->transf['lotacao'][0]['inCodRegime']);
$boErro = $obTTPALotacao->deletaLotacao();

$obTTPAConfiguraSituacaoFuncional->setDado( 'cod_sub_divisao', $sessao->transf['lotacao'][0]['inCodSubDivisao']);
$obTTPAConfiguraSituacaoFuncional->setDado( 'cod_regime', $sessao->transf['lotacao'][0]['inCodRegime']);
$obTTPAConfiguraSituacaoFuncional->deletaSituacaoFuncional();

foreach ($sessao->transf['lotacao'] as $key =>$valores) {
    $obTTPAConfiguraTipoCargo->setDado( 'cod_tipo', $valores['inCodTipoCargo']);
    foreach ($valores['cargos'] as $chave =>$codCargo) {
        $obTTPAConfiguraTipoCargo->setDado( 'cod_cargo', $codCargo);

        $obTTPAConfiguraTipoCargo->recuperaListagemConfiguraTipoCargo($rsConfigTiposCargo);
        if ($rsConfigTiposCargo->getNumLinhas() == 1) {
            $obTTPAConfiguraTipoCargo->excluirTipoCargo();
        }
    }
}

if (!$boErro->ocorreu()) {
    foreach ($sessao->transf['lotacao'] as $chave =>$valor) {
        $obTTPALotacao->setDado( 'cod_situacao'   , $valor['inCodSituacao']);
        $obTTPALotacao->setDado( 'cod_tipo', $valor['inCodTipoCargo']);

        $obTTPAConfiguraTipoCargo->setDado( 'cod_tipo', $valor['inCodTipoCargo']);

        $obTTPAConfiguraSituacaoFuncional->setDado( 'cod_situacao'   , $valor['inCodSituacao']);
        $obTTPAConfiguraSituacaoFuncional->setDado( 'cod_tipo', $valor['inCodTipoCargo']);
        $obTTPAConfiguraSituacaoFuncional->recuperaListagemConfiguraSituacaoFuncional($rsConfigSituacaoFuncional);
        if ($rsConfigSituacaoFuncional->getNumLinhas() < 1) {
            $obTTPAConfiguraSituacaoFuncional->inclusao();
        }
        foreach ($valor['cargos'] as $key =>$codCargo) {
            $obTTPALotacao->setDado( 'cod_cargo', $codCargo);
            $obTTPAConfiguraTipoCargo->setDado( 'cod_cargo', $codCargo);

            $obTTPAConfiguraTipoCargo->excluirTipoCargo();
            $obTTPAConfiguraTipoCargo->recuperaListagemConfiguraTipoCargo($rsConfigTiposCargo);
            if ($rsConfigTiposCargo->getNumLinhas() < 1) {
                $obTTPAConfiguraTipoCargo->inclusao();
                //echo "  inserindo tipos de cargo";
            }
            $obTTPALotacao->inclusao();
            //echo "inserindo lotacao";
        }
    }
    SistemaLegado::alertaAviso($pgForm."?inCodEntidade=".$sessao->getEntidade(), " ".$cont." Dados incluídos ", "alterar", "aviso", $sessao->id, "../");

} else {
    SistemaLegado::exibeAviso(urlencode($bobErro->getDescricao()),"n_incluir","erro");
}

SistemaLegado::LiberaFrames();

?>
