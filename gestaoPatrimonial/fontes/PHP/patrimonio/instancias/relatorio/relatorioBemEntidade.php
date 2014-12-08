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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FLManterBem.php 36840 2009-01-06 21:16:27Z luiz $

    * Casos de uso: uc-03.01.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php");
include_once( CAM_GP_COM_COMPONENTES."IPopUpFornecedor.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );

$stPrograma = "RelatorioBemEntidade";
$pgFilt   = "relatorioBemEntidade.php";
$pgOcul   = "OCGera".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction( $pgOcul );

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia o componente IMontaClassificacao
$obIMontaClassificacao = new IMontaClassificacao( $obForm );
$obIMontaClassificacao->obTxtCodClassificacao->setValue( $stClassificacao );
$obIMontaClassificacao->setNull( true );

//instancia um textbox para a descrição do bem
$obTxtDescricaoBem = new TextBox;
$obTxtDescricaoBem->setRotulo( 'Descrição' );
$obTxtDescricaoBem->setTitle( 'Informe a descrição do bem ' );
$obTxtDescricaoBem->setName( 'stDescricaoBem' );
$obTxtDescricaoBem->setSize( 60 );
$obTxtDescricaoBem->setMaxLength( 60 );
$obTxtDescricaoBem->setNull( true );
//instancia um tipobusca
$obTipoBuscaDescricaoBem = new TipoBusca( $obTxtDescricaoBem );

//instancia o componenete ITextBoxSelectEntidadeGeral
$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->setNull( false );

//instancia um compenente periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setRotulo( 'Período' );
$obPeriodicidade->setTitle( 'Selecione o período da data.' );
$obPeriodicidade->setNull( false );
$obPeriodicidade->setExercicio ( Sessao::getExercicio() );

$obSelectOrdenacao = new Select();
$obSelectOrdenacao->setRotulo( 'Ordenação' );
$obSelectOrdenacao->setTitle( 'Selecione a ordenação.' );
$obSelectOrdenacao->setName( 'stOrdenacao' );
$obSelectOrdenacao->addOption( '', 'Selecione' );
$obSelectOrdenacao->addOption( 'cod_bem', 'Código do Bem' );
$obSelectOrdenacao->addOption( 'descricao', 'Descrição do Bem' );
$obSelectOrdenacao->addOption( 'classificacao', 'Classificação' );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda("UC-03.01.20");
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Relatório de Bens por Entidade' );
$obIMontaClassificacao->geraFormulario( $obFormulario );
$obFormulario->addComponente	( $obTipoBuscaDescricaoBem);
$obFormulario->addComponente( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obSelectOrdenacao );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
