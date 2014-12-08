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
    * Página de Filtro - Exportação Arquivos EContas

    * Data de Criação   : 21/05/2014

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: FLExportacaoEContas.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoEContas";
$pgFilt= "FL".$stPrograma.".php";
$pgList= "LS".$stPrograma.".php";
$pgForm= "FM".$stPrograma.".php";
$pgProc= "PR".$stPrograma.".php";
$pgOcul= "OC".$stPrograma.".php";
$pgJS  = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('link');

$rsArqExport = $rsAtributos = new RecordSet;

$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue('../../../TCEAM/instancias/exportacao/'.$pgOcul);

$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ( "stTipoExport"             );
$obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais"     );
$obRdbTipoExportArqIndividual->setValue  ( "individuais"              );
$obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"      );
$obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"       );
$obRdbTipoExportArqIndividual->setChecked( true );

/* Tipo Arquivo Compactado */
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName  ( "stTipoExport"    );
$obRdbTipoExportArqCompactado->setLabel ( "Compactados"     );
$obRdbTipoExportArqCompactado->setValue ( "compactados"     );

$arNomeArquivos = array(
       'ADESAOATALICITACAO.REM.txt'
       ,'CERTIDAO.REM.txt'
       ,'CONTRATO.REM.txt'
       ,'CONTRATOEMPENHO.REM.txt'
       ,'CONVENIO.REM.txt'
       ,'CONVENIOEMPENHO.REM.txt'
       ,'COTACAO.REM.txt'
       ,'ITEMADESAOATA.REM.txt'
       ,'ITEMLICITACAO.REM.txt'
       ,'LICITACAO.REM.txt'
       ,'PARTICIPANTECONVENIO.REM.txt'
       ,'PARTICIPANTELICITACAO.REM.txt'
       ,'PUBLICACAO.REM.txt'
);

array_multisort($arNomeArquivos, SORT_ASC, SORT_STRING);

for ($inCounter=0;$inCounter < count($arNomeArquivos);$inCounter++) {
    $arElementosArq[$inCounter]['Arquivo'  ]   = $arNomeArquivos[$inCounter]  ;
    $arElementosArq[$inCounter]['Nome'     ]   = $arNomeArquivos[$inCounter]  ;
}

$obMes = new Mes();
$obMes->setNull( false );

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();

$rsArqSelecionados = new RecordSet;
$rsArqDisponiveis = new RecordSet;
$rsArqDisponiveis->preenche($arElementosArq);

$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName  ( 'arArquivosSelecionados'     );
$obCmbArquivos->setRotulo( 'Arquivos'                   );
$obCmbArquivos->setTitle ( 'Arquivos Disponiveis'       );
$obCmbArquivos->setNull  ( false );

// lista de ARQUIVOS disponiveis
$obCmbArquivos->SetNomeLista1( 'arCodArqDisponiveis'    );
$obCmbArquivos->setCampoId1  ( 'Arquivo'                );
$obCmbArquivos->setCampoDesc1( 'Nome'                   );
$obCmbArquivos->SetRecord1   ( $rsArqDisponiveis        );

// lista de ARQUIVOS selecionados
$obCmbArquivos->SetNomeLista2( 'arArquivosSelecionados' );
$obCmbArquivos->setCampoId2  ( 'Arquivo'                );
$obCmbArquivos->setCampoDesc2( 'Nome'                   );
$obCmbArquivos->SetRecord2   ( $rsArqSelecionados       );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( "../../../exportacao/instancias/processamento/PRExportador.php" );
$obForm->setTarget( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm             ( $obForm );
$obFormulario->addTitulo           ( "Dados para geração de arquivos" );
$obFormulario->addHidden           ( $obHdnAcao         );
$obFormulario->addComponente       ( $obISelectEntidade );
$obFormulario->agrupaComponentes   ( array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado) );
$obFormulario->addComponente       ( $obMes             );
$obFormulario->addComponente       ( $obCmbArquivos     );

$obFormulario->OK    ();
$obFormulario->show  ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
