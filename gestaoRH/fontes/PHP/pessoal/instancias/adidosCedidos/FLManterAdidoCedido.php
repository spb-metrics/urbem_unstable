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
    * Página de Filtro do Acidos Cedidos
    * Data de Criação: 28/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-04.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterAdidoCedido";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
Sessao::remove('link');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$jsOnload   = "montaParametrosGET('preencherSpanFiltro','stOpcao');";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obHdnEval =  new HiddenEval;
$obHdnEval->setName                             ( "stEval"                                                              );
$obHdnEval->setId                               ( "stEval"                                                              );
$obHdnEval->setValue                            ( $stEval                                                               );

$obRdoContrato = new Radio;
$obRdoContrato->setName                         ( "stOpcao"                                                             );
$obRdoContrato->setTitle                        ( "Selecione o tipo de filtro a ser utilizado." );
$obRdoContrato->setRotulo                       ( "Opções"                                                              );
$obRdoContrato->setLabel                        ( "Matrícula"                                                           );
$obRdoContrato->setValue                        ( "contrato"                                                            );
$obRdoContrato->obEvento->setOnChange           ( "montaParametrosGET('preencherSpanFiltro','stOpcao');"                           );
$obRdoContrato->setChecked                      ( $stOpcao == 'contrato' || !$stOpcao                                   );
$obRdoContrato->setNull(false);

$obRdoCgmContrato = new Radio;
$obRdoCgmContrato->setName                      ( "stOpcao"                                                             );
$obRdoCgmContrato->setTitle                     ( "Selecione o tipo de filtro a ser utilizado." );
$obRdoCgmContrato->setRotulo                    ( "Opções"                                                              );
$obRdoCgmContrato->setLabel                     ( "CGM/Matrícula"                                                       );
$obRdoCgmContrato->setValue                     ( "cgm_contrato"                                                        );
$obRdoCgmContrato->obEvento->setOnChange        ( "montaParametrosGET('preencherSpanFiltro','stOpcao');"                           );
$obRdoCgmContrato->setChecked                   ( $stOpcao == 'cgm_contrato'                                            );
$obRdoCgmContrato->setNull(false);

$obSpnFiltro = new Span;
$obSpnFiltro->setid                             ( "spnFiltro"                                                           );
$obSpnFiltro->setValue                          ( ""                                                                    );

$obBtnOk = new Ok;

$obBtnLimpar = new Ok();
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->obEvento->setOnClick              ( "executaFuncaoAjax('limparFiltro');"                                  );

//DEFINICAO DO FORM
$obForm = new Form;
if ($stAcao == "consultar") {
    $obForm->setAction                              ( $pgForm                                                               );
} else {
    $obForm->setAction                              ( $pgList                                                               );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnEval,true                                                       );
$obFormulario->addTitulo                        ( "Dados do Servidor"                                                   );
$obFormulario->agrupaComponentes                ( array($obRdoContrato,$obRdoCgmContrato)                               );
$obFormulario->addSpan                          ( $obSpnFiltro                                                          );
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
