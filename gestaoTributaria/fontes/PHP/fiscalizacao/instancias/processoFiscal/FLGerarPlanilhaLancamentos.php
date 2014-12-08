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
    * Página que Gera Planilha de Lançamentos
    * Data de Criacao: 12/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(CAM_GT_FIS_COMPONENTES."ITextBoxSelectTipoFiscalizacao.class.php");
require_once(CAM_GT_FIS_NEGOCIO."RFISGerarPlanilhaLancamentos.class.php");
require_once(CAM_GT_FIS_VISAO."VFISGerarPlanilhaLancamentos.class.php");
include_once (CAM_GT_FIS_NEGOCIO."/RFISProcessoFiscal.class.php");
include_once (CAM_GT_FIS_VISAO."/VFISProcessoFiscal.class.php");
$obControllerProcessoFiscal = new RFISProcessoFiscal;
$obVisaoProcessoFiscal = new VFISProcessoFiscal($obControllerProcessoFiscal);
//Instanciando a Classe de Controle e de Visao
$obController = new RFISGerarPlanilhaLancamentos;
$obVisao = new VFISGerarPlanilhaLancamentos($obController);

$stAcao = 'gerar';

//Define o nome dos arquivos PHP
$stPrograma = "GerarPlanilhaLancamentos";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

$where = " fpfc.cod_processo is null and ftf.cod_processo is null \n";
$obRsProcessoFiscalLevantamentos = $obVisao->recuperaTodosCodProcessoLevantamentos($where);

//echo "<pre>",print_r($obRsProcessoFiscalLevantamentos),"</pre>";

$inNumCgm = Sessao::read('numCgm');

//Campos Hidden
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue($_REQUEST['stCtrl']);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ("numcgm");
$obHdnCtrl->setValue($inNumCgm);

$obHdnInicio =  new Hidden;
$obHdnInicio->setName ("boInicio");
$obHdnInicio->setValue(true);

$obHdnInTipoFiscalizacao =  new Hidden;
$obHdnInTipoFiscalizacao->setName ("stTipoFiscalizacao");
$obHdnInTipoFiscalizacao->setValue("1");

//Definição do Form
$obForm = new Form;
$obForm->setAction ($pgForm);
$obForm->setTarget ("telaPrincipal");

//Tipo Fiscalizacao
$obTipoFiscalizacao = new ITextBoxSelectTipoFiscalizacao;
$obTipoFiscalizacao->setNull(true);
$obTipoFiscalizacao->setValue(1);
$obTipoFiscalizacao->setTitle("Informe o Tipo de Fiscalização.");
$obTipoFiscalizacao->obTxtTipoFiscalizacao->setId("txtTipoFiscalizacao");
$obTipoFiscalizacao->obCmbTipoFiscalizacao->setId("cmbTipoFiscalizacao");
$obTipoFiscalizacao->obTxtTipoFiscalizacao->setDisabled(true);
$obTipoFiscalizacao->obCmbTipoFiscalizacao->setDisabled(true);

//Processo Fiscal
$obProcessoNewFiscal = new Select;
$obProcessoNewFiscal->setName("inCodProcessoInscricao");
$obProcessoNewFiscal->setId("inCodProcessoInscricao");
$obProcessoNewFiscal->setMultiple(false);
$obProcessoNewFiscal->setStyle("width:400px;");
$obProcessoNewFiscal->setRotulo("Processo / Nome ou Razão Social");
$obProcessoNewFiscal->setTitle("Informe o Código do Processo Fiscal.");
$obProcessoNewFiscal->setNull(false);
$obProcessoNewFiscal->setCampoId("cod_inscricao");
$obProcessoNewFiscal->setCampoDesc("processo_nome");
$obProcessoNewFiscal->addOption("", "Selecione");
$obProcessoNewFiscal->preencheCombo($obRsProcessoFiscalLevantamentos);

//Novo Formulário
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnInicio);
$obFormulario->addHidden($obHdnInTipoFiscalizacao);
$obFormulario->addTitulo("Dados para Planilha de Lançamentos");
$obTipoFiscalizacao->geraFormulario($obFormulario);
$obFormulario->addComponente($obProcessoNewFiscal);
$obFormulario->Ok();
if (!$obVisaoProcessoFiscal->getFiscalAtivo()) {
   SistemaLegado::exibeAviso("Fiscal não Habilitado para este tipo de operação.","","erro");
   $obFormulario = new Formulario;
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
