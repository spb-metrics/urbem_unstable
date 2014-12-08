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
 * Arquivo filtro - Exportação arquivos Planejamento TCE/MG
 *
 * @category    Urbem
 * @package     TCE/MG
 * @author      Eduardo Schitz   <eduardo.schitz@cnm.org.br>
 * $Id: FLExportarAcompanhamentoMensal.php 59612 2014-09-02 12:00:51Z gelson $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportarAcompanhamentoMensal" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('link');

$rsArqExport    = $rsAtributos = new RecordSet;
$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue( "../exportacao/".$pgOcul );

$obEntidade = new ROrcamentoEntidade;
$obEntidade->obRCGM->setNumCGM ( Sessao::read('numCgm') );
$rsEntidadesDisponiveis  = new RecordSet;
$rsEntidadesSelecionadas = new RecordSet;
$obEntidade->listarUsuariosEntidade($rsEntidadesDisponiveis , " ORDER BY cod_entidade" );
$obEntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

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

/* Radio para selecionar tipo de exportacao*/
/* Tipo Arquivo Individual */
$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ( "stTipoExport"         );
$obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais" );
$obRdbTipoExportArqIndividual->setValue  ( "individuais"          );
$obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"  );
$obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"   );
$obRdbTipoExportArqIndividual->setChecked(true                    );
/* Tipo Arquivo Compactado */
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName  ( "stTipoExport" );
$obRdbTipoExportArqCompactado->setLabel ( "Compactados"  );
$obRdbTipoExportArqCompactado->setValue ( "compactados"  );

/* Lista Arquivos para Selecionar */
/* Elementos no array*/
$arNomeArquivos = array(
    'ABERLIC.csv',
    'AEX.csv',
    'ALQ.csv',
    'ANL.csv',
    'AOB.csv',
    'AOC.csv',
    'AOP.csv',
    'ARC.csv',
    'CAIXA.csv',
    'CONSID.csv',
    'CONSOR.csv',
    'CONTRATOS.csv',
    'CONV.csv',
    'CTB.csv',
    'CVC.csv',
    'DCLRF.csv',
    'DDC.csv',
    'DISPENSA.csv',
    'EMP.csv',
    'EXT.csv',
    'HABLIC.csv',
    'HOMOLIC.csv',
    'IDE.csv',
    'ITEM.csv',
    'JULGLIC.csv',
    'LAO.csv',
    'LQD.csv',
    'NTF.csv',
    'OBELAC.csv',
    'OPS.csv',
    'ORGAO.csv',
    'PAREC.csv',
    'PARELIC.csv',
    'PARPPS.csv',
    'PESSOA.csv',
    'REC.csv',
    'REGADESAO.csv',
    'REGLIC.csv',
    'RESPLIC.csv',
    'RSP.csv'
);


// Preenche array
for ($inCounter=0;$inCounter < count($arNomeArquivos);$inCounter++) {
    $arElementosArq[$inCounter]['Arquivo']   = $arNomeArquivos[$inCounter];
    $arElementosArq[$inCounter]['Nome'   ]   = $arNomeArquivos[$inCounter];
}

$rsArqSelecionados = new RecordSet;
$rsArqDisponiveis = new RecordSet;
$rsArqDisponiveis->preenche($arElementosArq);

$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName  ( 'arArquivosSelecionados' );
$obCmbArquivos->setRotulo( "Arquivos" );
$obCmbArquivos->setNull  ( false );
$obCmbArquivos->setTitle ( 'Arquivos Disponiveis' );

// lista de ARQUIVOS disponiveis
$obCmbArquivos->SetNomeLista1( 'arCodArqDisponiveis' );
$obCmbArquivos->setCampoId1  ( 'Arquivo' );
$obCmbArquivos->setCampoDesc1( 'Nome' );
$obCmbArquivos->SetRecord1   ( $rsArqDisponiveis   );

// lista de ARQUIVOS selecionados
$obCmbArquivos->SetNomeLista2( 'arArquivosSelecionados' );
$obCmbArquivos->setCampoId2  ( 'Arquivo' );
$obCmbArquivos->setCampoDesc2( 'Nome' );
$obCmbArquivos->SetRecord2   ( $rsArqSelecionados );

$arMes = array(
    '1' => 'Janeiro',
    '2' => 'Fevereiro',
    '3' => 'Março',
    '4' => 'Abril',
    '5' => 'Maio',
    '6' => 'Junho',
    '7' => 'Julho',
    '8' => 'Agosto',
    '9' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
);

if (Sessao::getExercicio() == date('Y')) {
    for ($i=1; $i < date('m'); $i++) {
        $arMes2[$i] = $arMes[$i];
    }

    $arMes = $arMes2;
}

$obCmbMes = new Select();
$obCmbMes->setName  ( 'stMes' );
$obCmbMes->setRotulo( "Mês" );
$obCmbMes->setNull  ( false );
$obCmbMes->setTitle ( 'Mês' );
$obCmbMes->setOptions( $arMes );

$obLbExercicio = new Label();
$obLbExercicio->setName  ( 'stExercicio' );
$obLbExercicio->setRotulo( 'Exercício' );
$obLbExercicio->setTitle ( 'Exercício' );
$obLbExercicio->setValue ( '/ '.Sessao::getExercicio());

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( "PRExportador.php" );
$obForm->setTarget( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm );
$obFormulario->addTitulo         ( "Dados para arquivos" );
$obFormulario->addHidden         ( $obHdnAcao            );
$obFormulario->addComponente     ( $obCmbEntidades       );
$obFormulario->agrupaComponentes (array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado));
$obFormulario->addComponente     ($obCmbArquivos);
$obFormulario->addComponenteComposto($obCmbMes, $obLbExercicio);
$obFormulario->OK                ();
$obFormulario->show              ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
