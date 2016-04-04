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
    * Página Filtro para Relatório Resumo Execução de Restos a Pagar
    * Data de Criação   : 24/02/2016

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: FLResumoExecucaoRP.php 64462 2016-02-25 17:57:26Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";

//Define o nome dos arquivos PHP
$stPrograma = 'ResumoExecucaoRP';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
while ( !$rsEntidades->eof() ) {
    $arFiltroNom['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

$rsRecordset = new RecordSet();

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/".$pgOcul );

$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "inCodModulo" );
$obHdnModulo->setValue( $request->get('modulo') );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
$obCmbEntidades->setNull   ( false );

// Ações disparadas por eventos
$obCmbEntidades->obSelect1->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obCmbEntidades->obSelect2->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao1->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao2->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao3->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao4->obEvento->setOnClick('getIMontaAssinaturas()');

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

// Monta o componente de Periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio(Sessao::getExercicio());
$obPeriodicidade->setNull     (false);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                                 );
$obFormulario->addHidden    ( $obHdnCaminho                           );
$obFormulario->addHidden    ( $obHdnModulo                            );
$obFormulario->addTitulo    ( "Dados para Filtro"                     );
$obFormulario->addComponente( $obCmbEntidades                         );
$obFormulario->addComponente( $obPeriodicidade                        );

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>
