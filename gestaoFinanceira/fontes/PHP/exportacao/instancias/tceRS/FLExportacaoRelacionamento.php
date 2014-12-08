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
    * Página de Filtro - Exportação Arquivos de Relacionamento
    * Data de Criação   : 31/01/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-17 11:32:12 -0300 (Seg, 17 Jul 2006) $

    * Casos de uso: uc-02.08.03
*/

/*
$Log$
Revision 1.8  2006/07/17 14:30:48  cako
Bug #6013#

Revision 1.7  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoRelacionamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Inclui arquivo JavaScript
include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//destroi arrays de sessão que armazenam os dados do FILTRO
unset( $sessao->link );
unset( $sessao->filtro );
unset( $sessao->transf3 );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue( "../tceRS/".$pgOcul );

//Define Array com os arquivos disponiveis
$arArquivosDisponiveis[0]['Arquivo'] 	=  "CTA_DISP.TXT";
$arArquivosDisponiveis[0]['Nome']       =  "CTA_DISP.TXT";
$arArquivosDisponiveis[1]['Arquivo'] 	=  "CTA_OPER.TXT";
$arArquivosDisponiveis[1]['Nome']	    =  "CTA_OPER.TXT";

//Cria um Recordset baseado neste array
$rsArquivosSelecionados = new Recordset;
$rsArquivosDisponiveis = new Recordset;
$rsArquivosDisponiveis->preenche($arArquivosDisponiveis);

//Cria coombo para seleção do Período
$obCmbPeriodoExport = new Select();
$obCmbPeriodoExport->setRotulo			( "*Periodo" 	)	;
$obCmbPeriodoExport->setName          	( "inPeriodo" 	)	;
$obCmbPeriodoExport->setId          	( "inPeriodo" 	)	;

// Monta array de bimestres e cria laço que adiciona ao combo
$arBimExport = array(1=>"1o Bimestre",2=>"2o Bimestre",3=>"3o Bimestre",4=>"4o Bimestre",5=>"5o Bimestre",6=>"6o Bimestre");
for ($inContandorOpt=1;$inContandorOpt <= 6;$inContandorOpt++) {
   $obCmbPeriodoExport->addOption($inContandorOpt,$arBimExport[$inContandorOpt]);
}

$obEntidade = new ROrcamentoEntidade;
$obEntidade->obRCGM->setNumCGM ( Sessao::read('numCgm') );
$rsEntidadesDisponiveis  = new RecordSet;
$rsEntidadesSelecionadas = new RecordSet;
$obEntidade->listarUsuariosEntidade($rsEntidadesDisponiveis , " ORDER BY cod_entidade" );
$obEntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

// select com setores do governo
$obTxtSetorGoverno = new Select();
$obTxtSetorGoverno->setRotulo       ( "Setor do Governo"            );
$obTxtSetorGoverno->setName         ( "stCnpjSetor"                 );
$obTxtSetorGoverno->setId           ( "stCnpjSetor"                 );
$obTxtSetorGoverno->setCampoID      ( "[cnpj]|[nom_cgm]"            );
$obTxtSetorGoverno->setCampoDesc    ( "nom_cgm"                     );
$obTxtSetorGoverno->preencheCombo   ( $rsEntidadesDisponiveisCnpj   );
// Lista ENTIDADES para Selecionar
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName  ( 'arEntidadesSelecionadas' );
$obCmbEntidades->setRotulo( "Entidade" );
$obCmbEntidades->setNull  ( false );
$obCmbEntidades->setTitle ( 'Entidades Disponiveis' );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidadesDisponiveis->getNumLinhas()==1) {
       $rsEntidadesSelecionadas = $rsEntidadesDisponiveis;
       $rsEntidadesDisponiveis = new RecordSet;
}

// Lista de ENTIDADES disponiveis
$obCmbEntidades->SetNomeLista1( 'arEntidadesDisponiveis' );
$obCmbEntidades->setCampoId1  ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1( '[cod_entidade] - [nom_cgm]' );
$obCmbEntidades->SetRecord1   ( $rsEntidadesDisponiveis   );

// lista de ENTIDADES selecionadas
$obCmbEntidades->SetNomeLista2( 'arEntidadesSelecionadas' );
$obCmbEntidades->setCampoId2  ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc2( '[cod_entidade] - [nom_cgm]' );
$obCmbEntidades->SetRecord2   ( $rsEntidadesSelecionadas );

//Define objeto Radio para Tipo de Exportação
$obRdTipoExportacao1 = new Radio;
$obRdTipoExportacao1->setName       ( "boTipoExportacao" );
$obRdTipoExportacao1->setValue      ( "1" );
$obRdTipoExportacao1->setChecked    ( true );
$obRdTipoExportacao1->setLabel      ( "Aquivos Individuais" );

$obRdTipoExportacao2 = new Radio;
$obRdTipoExportacao2->setName       ( "boTipoExportacao" );
$obRdTipoExportacao2->setValue      ( "2" );
$obRdTipoExportacao2->setChecked    ( false );
$obRdTipoExportacao2->setLabel      ( "Arquivo Compactado" );

//Define os objetos COMBO para a selecao dos ARQUIVOS
$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName     ( 'arArquivosSelecionados' );
$obCmbArquivos->setRotulo   ( "Arquivos" );
$obCmbArquivos->setNull     ( false );
$obCmbArquivos->setTitle    ( 'Arquivos de relacionamento das Contas do Exercício Atual' );

// lista de ARQUIVOS disponíveis
$obCmbArquivos->SetNomeLista1       ( 'arArquivosDisponiveis' );
$obCmbArquivos->setCampoId1         ( 'Arquivo' );
$obCmbArquivos->setCampoDesc1       ( 'Nome' );
$obCmbArquivos->SetRecord1          ( $rsArquivosDisponiveis );
//$obCmbArquivos->obSelect1->setStyle ( 'width: 320px' );

// lista de ARQUIVOS selecionados
$obCmbArquivos->SetNomeLista2       ( 'arArquivosSelecionados' );
$obCmbArquivos->setCampoId2         ( 'Arquivo' );
$obCmbArquivos->setCampoDesc2       ( 'Nome' );
$obCmbArquivos->SetRecord2          ( $rsArquivosSelecionados );
//$obCmbArquivos->obSelect2->setStyle ( 'width: 320px' );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( "../processamento/PRExportador.php"   );
$obForm->setTarget      ( "telaPrincipal"                       ); //oculto - telaPrincipal

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm );
$obFormulario->addHidden        ( $obHdnAcao );
$obFormulario->addTitulo        ( "Dados para filtro");
$obFormulario->addComponente    ( $obCmbPeriodoExport);
$obFormulario->addComponente    ( $obTxtSetorGoverno );
$obFormulario->addComponente    ( $obCmbEntidades    );
//$obFormulario->addComponente    ( $obTxtOrgaoUnidade );
$obFormulario->abreLinha        ();
$obFormulario->addRotulo        ( "*Tipo de Exportação","*Tipo de Exportação" );
$obFormulario->addCampo         ( $obRdTipoExportacao1, true, false );
$obFormulario->addCampo         ( $obRdTipoExportacao2, false, true );
$obFormulario->fechaLinha       ();
$obFormulario->addComponente    ( $obCmbArquivos );
$obFormulario->setFormFocus     ($obCmbPeriodoExport->getId());
$obFormulario->OK               ();
$obFormulario->show             ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
