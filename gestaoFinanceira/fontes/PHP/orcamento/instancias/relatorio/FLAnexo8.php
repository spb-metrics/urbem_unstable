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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 18/08/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2007-08-14 12:46:14 -0300 (Ter, 14 Ago 2007) $

    * Casos de uso: uc-02.01.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "Anexo8";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$rsRecordset = new RecordSet;

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/OCAnexo8.php" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValue          ( 4                 );

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Demonstrar Valores"            );
$obCmbSituacao->setTitle               ( "Selecione demonstrar valores." );
$obCmbSituacao->setName                ( "stSituacao"                    );
$obCmbSituacao->setValue               ( 3                               );
$obCmbSituacao->setStyle               ( "width: 200px"                  );
$obCmbSituacao->addOption              ( "", "Selecione"                 );
$obCmbSituacao->addOption              ( "empenhados", "Empenhados"      );
$obCmbSituacao->addOption              ( "liquidados", "Liquidados"      );
$obCmbSituacao->addOption              ( "pagos", "Pagos"                );
$obCmbSituacao->setNull                ( true                            );

$obCmbIntra= new Select;
$obCmbIntra->setRotulo              ( "Somente Intra-Orçamentárias"   );
$obCmbIntra->setTitle               ( "Selecione" );
$obCmbIntra->setName                ( "stIntra"                    );
$obCmbIntra->setValue               (  false                             );
$obCmbIntra->setStyle               ( "width: 200px"                  );
$obCmbIntra->addOption              ( "", "Selecione"                 );
$obCmbIntra->addOption              ( true, "Sim"      );
$obCmbIntra->addOption              ( false, "Nao"      );
$obCmbIntra->setNull                ( true                            );

$obCmbTipoRelatorio= new Select;
$obCmbTipoRelatorio->setRotulo  ( "Tipo de Relatório"      );
$obCmbTipoRelatorio->setTitle   ( "Selecione o tipo de relatório." );
$obCmbTipoRelatorio->setName    ( "stTipoRelatorio"         );
$obCmbTipoRelatorio->setValue   ( 3                         );
$obCmbTipoRelatorio->setStyle   ( "width: 200px"            );
$obCmbTipoRelatorio->addOption  ( "balanco", "Balanço"      );
$obCmbTipoRelatorio->addOption  ( "orcamento", "Orçamento"  );
$obCmbTipoRelatorio->setNull    ( false                     );
$obCmbTipoRelatorio->obEvento->setOnChange( "if (document.frm.stTipoRelatorio.value=='orcamento') {document.frm.stSituacao.value='';document.frm.stSituacao.disabled=true;} else {document.frm.stSituacao.disabled=false;}");

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->setAjuda ( 'UC-02.01.15');
$obFormulario->addForm      ( $obForm               );
$obFormulario->addHidden    ( $obHdnCaminho         );
$obFormulario->addTitulo    ( "Dados para Filtro"   );
$obFormulario->addComponente( $obCmbEntidades       );
$obFormulario->addComponente( $obPeriodicidade      );
$obFormulario->addComponente( $obCmbTipoRelatorio   );
$obFormulario->addComponente( $obCmbSituacao        );
$obFormulario->addComponente( $obCmbIntra           );

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

include_once ($pgJS);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
