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
    * Página de Processamento para Arquivar Processo em Lote.
    * Data de Criação: 23/04/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.98

    $Id: PRManterProcessoEmLote.php 62418 2015-05-06 17:45:05Z diogo.zarpelon $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_PROT_MAPEAMENTO."TProcessoArquivado.class.php"									);
include_once ( CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php"												);
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoria.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcessoEmLote";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obTProcessoArquivado = new TProcessoArquivado();
$obTAuditoria         = new TAuditoria;
$obTProcesso          = new TProcesso();
$obErro               = new Erro();

$inCodArquivamento = $_POST['stTipo'];
$arDados           = $_POST;
$stAcao            = $_REQUEST["stAcao"];

foreach ($arDados as $key => $value) {
    if (substr($key, 0,10) == "boArquivar" ) {
        $arProcessos[] = $value;
    }
}

/*  { Legado }
 *  Devido a inclusão de multi-requerentes, é necessário unificar o array
 *  para que não tenha duplicidade no código do processo.
 *  Quando o módulo for refeito, terá uma table-tree para organizar
 *  os multi-requerentes na tela de listagem, sem precisar listar mais
 *  de uma vez o mesmo processo devido ao multi-requerentes.
 */

$arAux   = array();
$arArray = array();

$arProcessos = array_unique($arProcessos);
$arAux = array_values($arProcessos);

$rsProcessos = new RecordSet();
$rsProcessos->preenche($arAux);

$id = 0;
while ( !$rsProcessos->eof() ) {

    $arArray = explode("/",$arAux[$id]);
    $codProcesso = $arArray[0];
    $anoExercicio = $arArray[1];

    $obTProcesso->setDado('cod_processo', $codProcesso);
    $obTProcesso->setDado('ano_exercicio',$anoExercicio);
    $obTProcesso->consultar();

    $obTAuditoria->setDado("numcgm",Sessao::read('numCgm'));
    $obTAuditoria->setDado("cod_acao",Sessao::read('acao'));
    $obTAuditoria->setDado("objeto",$codProcesso.'/'.$anoExercicio);
    $obTAuditoria->inclusao();

    $obTProcessoArquivado->setDado("cod_processo"       , $codProcesso 		);
    $obTProcessoArquivado->setDado("ano_exercicio"      , $anoExercicio		);
    $obTProcessoArquivado->setDado("cod_historico"      , $_POST['stHistorico'] );
    $obTProcessoArquivado->setDado("timestamp_arquivamento", date( "Y-m-d H:i:s.ms" ));
    $obTProcessoArquivado->setDado("texto_complementar" , $_POST['txtComplementar'] );
    $obTProcessoArquivado->setDado("localizacao"        , $_POST['stLocalizacaoFisica'] );
    $obTProcessoArquivado->setDado("cgm_arquivador"     , Sessao::read("numCgm"));
    $obErro = $obTProcessoArquivado->inclusao();

    $obTProcesso->setDado("cod_situacao", $_POST['stTipo']);
    $obTProcesso->alteracao();

    $id = $id + 1;
    $rsProcessos->proximo();

}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Processo(s) arquivado(s) com sucesso!","aviso","aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Erro auditado","n_incluir","erro", Sessao::getId(), "../");
}
