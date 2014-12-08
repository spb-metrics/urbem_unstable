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
    * Página de Lista do Calculo de Rescisão
    * Data de Criação: 18/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.18

    $Id: LSManterCalculoRescisao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php"                 );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

SistemaLegado::LiberaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoRescisao";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgProcImpressao = "PR".$stPrograma."Impressao.php";
$jsOnload   = "executaFuncaoAjax('gerarSpanSucessoErro');";

include_once($pgJS);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stStrl                                               );

$obRdoCalculados = new Radio;
$obRdoCalculados->setName                       ( "stOpcao"                                             );
$obRdoCalculados->setId                         ( "stOpcao"                                             );
$obRdoCalculados->setRotulo                     ( "Matrículas"                                           );
$obRdoCalculados->setLabel                      ( "Calculados com sucesso"                              );
$obRdoCalculados->setValue                      ( "calculados"                                          );
$obRdoCalculados->obEvento->setOnChange         ( "montaParametrosGET('gerarSpanSucessoCalculo','stOpcao');");
$obRdoCalculados->setChecked                    ( $stOpcao == 'calculados' || !$stOpcao                 );

$obRdoErro = new Radio;
$obRdoErro->setName                             ( "stOpcao"                                             );
$obRdoErro->setId                               ( "stOpcao"                                             );
$obRdoErro->setRotulo                           ( "Matrículas"                                           );
$obRdoErro->setLabel                            ( "Erro no cálculo"                                     );
$obRdoErro->setValue                            ( "erro"                                                );
$obRdoErro->obEvento->setOnChange               ( "montaParametrosGET('gerarSpanErroCalculo','stOpcao');");
$obRdoErro->setChecked                          ( $stOpcao == 'erro'                                    );

$obLblContratos = new Label;
$obLblContratos->setName                        ( "inQuantContratos"                                   );
$obLblContratos->setRotulo                      ( "Matrículas Calculadas"                              );
$obLblContratos->setValue                       (  Sessao::read("inContratosCalculados")               );

$obLblContratosSucesso = new Label;
$obLblContratosSucesso->setId                   ( "inQuantContratosSucesso"                            );
$obLblContratosSucesso->setRotulo               ( "Matrículas Calculadas com Sucesso"                  );

$obLblContratosErro = new Label;
$obLblContratosErro->setId                      ( "inQuantContratosErro"                               );
$obLblContratosErro->setRotulo                  ( "Matrículas Calculadas com Erro"                     );

//Define objeto SPAN
$obSpnSpan1 = new Span;
$obSpnSpan1->setId                              ( "spnSpan1"                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProcImpressao                                     );
$obForm->setTarget                              ( "telaPrincipal"                                      );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right");
$obFormulario->agrupaComponentes                ( array($obRdoCalculados,$obRdoErro)                    );
$obFormulario->addComponente                    ( $obLblContratos                                       );
$obFormulario->addComponente                    ( $obLblContratosSucesso                                       );
$obFormulario->addComponente                    ( $obLblContratosErro                                       );
$obFormulario->addSpan                          ( $obSpnSpan1                                           );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
