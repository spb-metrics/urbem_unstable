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
    * Página de Processamento de Configuração do módulo Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 31087 $
    $Name$
    $Author: luciano $
    $Date: 2007-07-03 16:45:40 -0300 (Ter, 03 Jul 2007) $

    * Casos de uso: uc-02.03.01, uc-02.03.04, uc-02.03.05
*/

/*
$Log$
Revision 1.9  2007/07/03 19:45:40  luciano
Bug#9451#

Revision 1.8  2007/07/03 19:37:59  luciano
Bug#9451#

Revision 1.7  2007/07/03 15:29:59  luciano
Bug#9451#

Revision 1.6  2006/07/05 20:47:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_EMP_NEGOCIO. "REmpenhoConfiguracao.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new REmpenhoConfiguracao;
$obRegra->setNumeracao ( $_POST['stTipoNumeracao'] );

/*if ($_POST['boAnularAutorizacaoAutomatica'] == "Sim") {
    $boAnularAutorizacaoAutomatica = "true";
} else {
    $boAnularAutorizacaoAutomatica = "false";
}
$obRegra->setAnularAutorizacaoAutomatica ( $boAnularAutorizacaoAutomatica );*/
$obRegra->setAnularAutorizacaoAutomatica ( true );

if ($_POST['boDataVencimento'] == "Sim") {
    $boDataVencimento = "true";
} else {
    $boDataVencimento = "false";
}
$obRegra->setDataVencimento ( $boDataVencimento );

if ($_POST['boLiquidacaoAutomatica'] == "Sim") {
    $boLiquidacaoAutomatica = "true";
} else {
    $boLiquidacaoAutomatica = "false";
}
$obRegra->setLiquidacaoAutomatica ( $boLiquidacaoAutomatica );

if ($_POST['boOPAutomatica'] == "Sim") {
    $boOPAutomatica = "true";
} else {
    $boOPAutomatica = "false";
}
$obRegra->setOPAutomatica ( $boOPAutomatica );

if ($_POST['boOPCarne'] == "Sim") {
    $boOPCarne = "true";
} else {
    $boOPCarne = "false";
}
$obRegra->setEmitirCarneOP($boOPCarne);

$obErro = $obRegra->salvar();

// Insere as contas caixa para as entidades
if (is_array(Sessao::read('arItens'))) {

    $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;
    $stFiltro = " WHERE parametro = 'conta_caixa' AND exercicio = '".Sessao::getExercicio()."' ";
    $obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsJaForam, $stFiltro);
    while ( !$rsJaForam->eof() ) {
        $stKeyDb = $rsJaForam->getCampo('exercicio').'-'.
                   $rsJaForam->getCampo('cod_entidade').'-'.
                   $rsJaForam->getCampo('cod_modulo').'-'.
                   $rsJaForam->getCampo('parametro');

        $arItensChave[$stKeyDb] = true;
        $rsJaForam->proximo();
    }

    // Inclui os dados
    $arItens = Sessao::read('arItens');
    foreach ($arItens as $key => $value) {

        $stKeyNew = Sessao::getExercicio().'-'.$value['inCodEntidade'].'-10-conta_caixa';

        $obTAdministracaoConfiguracaoEntidade->setDado( 'exercicio'    , Sessao::getExercicio()      );
        $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_entidade' , $value['inCodEntidade'] );
        $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_modulo'   , 10                      );
        $obTAdministracaoConfiguracaoEntidade->setDado( 'parametro'    , 'conta_caixa'           );
        $obTAdministracaoConfiguracaoEntidade->setDado( 'valor'        , $value['inCodConta']    );

        if ( !isset( $arItensChave[$stKeyNew] ) ) {

            $obTAdministracaoConfiguracaoEntidade->inclusao();
            unset( $arItensChave[$stKeyNew] );
        } else {
            $obTAdministracaoConfiguracaoEntidade->alteracao();
            unset( $arItensChave[$stKeyNew] );
        }

    }

    // Exclui os dados que restaram
    if (is_array($arItensChave)) {
        foreach ($arItensChave as $stChave => $valor) {

            $arChave = explode('-',$stChave);

            $obTAdministracaoConfiguracaoEntidade->setDado( 'exercicio'        , $arChave[0] );
            $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_entidade'     , $arChave[1] );
            $obTAdministracaoConfiguracaoEntidade->setDado( 'cod_modulo'       , $arChave[2] );
            $obTAdministracaoConfiguracaoEntidade->setDado( 'parametro'        , $arChave[3] );
            $obTAdministracaoConfiguracaoEntidade->exclusao();

        }
    }

}

if ( !$obErro->ocorreu() )
    SistemaLegado::alertaAviso($pgForm,"Configuração do Empenho","alterar","aviso", Sessao::getId(), "../");
else
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

?>
