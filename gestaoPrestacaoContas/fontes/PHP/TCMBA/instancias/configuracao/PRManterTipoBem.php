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
    * Página de Processamento - Parâmetros do Arquivo
    * Data de Criação   : 24/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:17:23  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/09/25 21:44:35  hboaventura
adicionando arquivos

Revision 1.1  2007/09/25 01:14:45  diego
Primeira versão.

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCMBA_MAPEAMENTO ."TTBATipoBem.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoBem";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro();
$obPersistente = new TTBATipoBem();
$obPersistente->recuperaTodos( $rsExclusao );
$obPersistente->debug();
while ( !$rsExclusao->eof() ) {
    $obPersistente->setDado( 'cod_tipo_tcm', $rsExclusao->getCampo('cod_tipo_tcm') );
    $obPersistente->exclusao();
    $rsExclusao->proximo();
}
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stAcao = 'incluir';
//echo $stAcao;
SistemaLegado::mostraVar($_REQUEST);
foreach ($_POST as $key=>$value) {
    if (strstr($key,"inTipo")) {
        $arIdentificador = explode('_',$key);
        $inCod = $arIdentificador[1];
        if (trim($value) <> "") {
            $arNaturezaGrupo = explode( "_", $value );
            $obPersistente->setDado('cod_tipo_tcm' ,$value);
            $obPersistente->setDado('cod_natureza' ,$arIdentificador[1]);
            $obPersistente->setDado('cod_grupo'    ,$arIdentificador[2]);
            $obErro = $obPersistente->inclusao();
            if( $obErro->ocorreu() )
                break;
        }
    }
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
}

SistemaLegado::LiberaFrames();

?>
