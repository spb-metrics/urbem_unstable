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
    * Página de Filtro para relatorio Modelo 11
    * Data de Criação   : 15/08/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Leandro André Zis

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso uc-02.05.13

    * @ignore
*/

/*
$Log$
Revision 1.5  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_LRF_NEGOCIO."RLRFTCERSModelo.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "Modelo11";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRLRFTCEModelo = new RLRFTCERSModelo();
$obRLRFTCEModelo->setExercicio( Sessao::getExercicio() );
$obRLRFTCEModelo->addQuadro();
$obRLRFTCEModelo->roUltimoQuadro->addContaPlano();
$obRLRFTCEModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRLRFTCEModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRLRFTCEModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$inCount = 0;
while ( !$rsEntidade->eof() ) {
    if ( strstr( strtolower( $rsEntidade->getCampo( 'nom_cgm' ) ), strtolower( 'câmara municipal' ) ) ) {
        $inCodEntidade = $rsEntidade->getCampo( 'cod_entidade' );
        $sessao->nomFiltro['nom_entidade'][$rsEntidade->getCampo( 'cod_entidade' )] = $rsEntidade->getCampo( 'nom_cgm' );
    }
    $rsEntidade->proximo();
}

$arMes = array( '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março'    , '04' => 'Abril'  , '05' => 'Maio'    , '06' => 'Junho',
                '07' => 'Julho'  , '08' => 'Agosto'   , '09' => 'Setembro' , '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro' );

foreach ($arMes as $inMes => $nomMes) {
    $sessao->nomFiltro['nom_mes'][$inMes] = $nomMes;
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

$sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_LRF_INSTANCIAS."tceRS/OCModelo11.php" );

$obHdnEntidade = new Hidden();
$obHdnEntidade->setName ( 'inCodEntidade' );
$obHdnEntidade->setValue( $inCodEntidade  );

//Define o objeto SELECT para mes
$obCmbMes = new Select();
$obCmbMes->setName      ( "inMes"           );
$obCmbMes->setValue     ( ''                );
$obCmbMes->setRotulo    ( "Mês"             );
$obCmbMes->setNull      ( false             );
$obCmbMes->setTitle     ( 'Selecione o mês' );
$obCmbMes->addOption    ( '', 'Selecione'   );
foreach ($arMes as $cod_mes => $nom_mes) {
    $obCmbMes->addOption    ( $cod_mes, $nom_mes );
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnCaminho  );
$obFormulario->addHidden( $obHdnEntidade );

$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente( $obCmbMes       );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
