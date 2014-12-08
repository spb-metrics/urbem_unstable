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
    * Página de Formulario de Configuração de LOA
  * Data de Criação: 15/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: $
  *
  * $Revision: $
  * $Name: $
  * $Author: $
  * $Date: $
  *
***/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoLOA";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once($pgOcul);

if ($request->get('stAcao') == '') {
    $stAcao = 'incluir';
} else {
    $stAcao = $request->get('stAcao');
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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
$obHdnCtrl->setId   ( "stCtrl" );

### Leis de Alteracao do PPA ###
$obIBuscaInnerLeiAlteracaoPPA = new IBuscaInnerNorma(false,true);
$obIBuscaInnerLeiAlteracaoPPA->obBscNorma->setRotulo('Lei Orçamentária Anual');

### 1 – Abertura de créditos suplementares (deve permitir digitação do percentual )
$obPorAberturaCredito = new Porcentagem();
$obPorAberturaCredito->setRotulo('Abertura de créditos suplementares');
$obPorAberturaCredito->setName('nuAberturaCredito');
$obPorAberturaCredito->setId('nuAberturaCredito');

### 2 – Contratação de operações de crédito (deve permitir digitação do percentual )
$obPorContratoCredito = new Porcentagem();
$obPorContratoCredito->setRotulo('Contratação de operações de crédito');
$obPorContratoCredito->setName('nuPorContratoCredito');
$obPorContratoCredito->setId('nuPorContratoCredito');

### 3 – Contratação de operações de crédito por antecipação de receita (deve permitir digitação do percentual )
$obPorContratoCreditoReceita = new Porcentagem();
$obPorContratoCreditoReceita->setRotulo('Contratação de operações de crédito por antecipação de receita');
$obPorContratoCreditoReceita->setName('nuPorContratoCreditoReceita');
$obPorContratoCreditoReceita->setId('nuPorContratoCreditoReceita');

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Configuração de LOA" );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obIBuscaInnerLeiAlteracaoPPA->geraFormulario($obFormulario);
$obFormulario->addTitulo     ( "Percentuais Autorizados LOA" );
$obFormulario->addComponente ( $obPorAberturaCredito );
$obFormulario->addComponente ( $obPorContratoCredito );
$obFormulario->addComponente ( $obPorContratoCreditoReceita );

$obFormulario->OK();
$obFormulario->show();

processarForm(true,"Form",$stAcao);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
