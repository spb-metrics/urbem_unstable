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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 24/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2008-08-18 09:58:01 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.3  2007/10/02 18:17:29  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.2  2007/09/25 21:44:47  hboaventura
adicionando arquivos

Revision 1.1  2007/09/25 01:14:45  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCMBA_MAPEAMENTO ."TTBATipoBem.class.php" );

SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoBem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//SistemaLegado::executaFramePrincipal( "buscaDado('MontaListaUniOrcam');" );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obPersistente = new TTBATipoBem();

$obPersistente->recuperaRelacionamento($rsRecordSetLista);
$obPersistente->recuperaNaturezaGrupo($rsRecordSetCombo,' ORDER BY g.cod_natureza, g.cod_grupo');

// SistemaLegado::mostravar($rsRecordSetLista);
// SistemaLegado::mostravar($rsRecordSetCombo);

$obLista = new Lista;
$obLista->setTitulo ( "Relacionamento com Documentos Exigidos - Tipos de Certidão TCMBA");
$obLista->setRecordSet ($rsRecordSetLista );
$obLista->setMostraPaginacao( false );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Bem - TCM" );
$obLista->ultimoCabecalho->setWidth( 67 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza/Grupo - Sistema" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_tipo_tcm] - [descricao]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obCmbCombo = new Select();
$obCmbCombo->setName          ("inTipo_[cod_tipo_tcm]_"     );
$obCmbCombo->setTitle         ("Selecione"                  );
$obCmbCombo->setRotulo        (""                           );
$obCmbCombo->addOption        ("","Selecione"               );
$obCmbCombo->setCampoId       ("[cod_natureza]_[cod_grupo]" );
$obCmbCombo->setCampoDesc     ("[cod_natureza]-[nom_natureza] , [cod_grupo]-[nom_grupo]"    );
$obCmbCombo->preencheCombo    ($rsRecordSetCombo            );
$obCmbCombo->setNull          ( false                       );
$obCmbCombo->setValue         ("[cod_natureza]_[cod_grupo]");

$obLista->addDadoComponente( $obCmbCombo );
$obLista->ultimoDado->setCampo( "[cod_natureza]_[cod_grupo]" );
$obLista->commitDadoComponente();
$obLista->montaInnerHTML();

$stLista = $obLista->getHTML();

//Define Span para DataGrid
$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );
$obSpnLista->setValue ( $stLista );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addSpan( $obSpnLista );

$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
