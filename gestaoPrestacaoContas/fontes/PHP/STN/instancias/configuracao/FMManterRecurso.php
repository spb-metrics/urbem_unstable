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
    * Página de Formulário para Configuração
    * Data de Criação  : 15/05/2008

    * @author Analista Gelson W. Golçalves
    * @author Desenvolvedor Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * $Id: FMManterRecurso.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.01.09

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
require_once CAM_GF_ORC_COMPONENTES."ISelectOrgao.class.php";
require_once CAM_GF_ORC_COMPONENTES."ISelectUnidade.class.php";
require_once CAM_GF_ORC_COMPONENTES."ISelectMultiploRecurso.class.php";
require_once CAM_GPC_STN_MAPEAMENTO."TSTNVinculoRecurso.class.php";

$stPrograma = "ManterRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTSTNVinculoRecurso = new TSTNVinculoRecurso;
$obTSTNVinculoRecurso->recuperaRelacionamento( $rsVinculoRelacionamento );

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodUnidade = new Hidden;
$obHdnCodUnidade->setName( "inCodigosUnidade" );
$obHdnCodUnidade->setValue( "" );

$stJs = "montaParametrosGET( 'montaDadosRecurso', 'inCodEntidade, stCodEntidade, inCodOrgao, inCodUnidade, stAcao' )";
$obInCodEntidade = new ITextBoxSelectEntidadeGeral;
$obInCodEntidade->setExercicio( Sessao::getExercicio() );
$obInCodEntidade->setNull(false);
$obInCodEntidade->obTextBox->obEvento->setOnChange($stJs);
$obInCodEntidade->obSelect->obEvento->setOnChange($stJs);

$obInCodOrgao = new ISelectOrgao;
$obInCodOrgao->setExercicio( Sessao::getExercicio() );
$obInCodOrgao->setNull(false);
$obInCodOrgao->obEvento->setOnChange("montaParametrosGET( 'montaDadosUnidade', this.name, true ); ".$stJs);

$obInCodUnidade = new ISelectUnidade;
$obInCodUnidade->setExercicio( Sessao::getExercicio() );
$obInCodUnidade->setNull(false);
$obInCodUnidade->obEvento->setOnChange($stJs);

if ($stAcao == '1') {
    $obISelectMultiplRecurso2 = new ISelectMultiploRecurso;
    $obISelectMultiplRecurso2->setName("inCodRecurso2");
    $obISelectMultiplRecurso2->setNomeLista1 ("inCodRecursoDisponivel2");
    $obISelectMultiplRecurso2->setNomeLista2 ("inCodRecursoSelecionado2");
    $obISelectMultiplRecurso2->setRotulo("Recursos de Pagamento de Profissionais Magistério");
    $obISelectMultiplRecurso2->setExercicio( Sessao::getExercicio() );
    $obISelectMultiplRecurso2->setCarregarDados( false );
    $obISelectMultiplRecurso2->setFiltro( $stFiltro );
}

$obISelectMultiplRecurso = new ISelectMultiploRecurso;
if ($stAcao == '1') {$obISelectMultiplRecurso->setRotulo("Recursos de Outras Despesas");}
$obISelectMultiplRecurso->setExercicio( Sessao::getExercicio() );
$obISelectMultiplRecurso->setCarregarDados( false );
$obISelectMultiplRecurso->setFiltro( $stFiltro );

$stTitulo = SistemaLegado::pegaDado('descricao', 'stn.vinculo_stn_recurso', 'WHERE vinculo_stn_recurso.cod_vinculo = '.$stAcao);

//DEFINICAO DOS COMPONENTES

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnCodUnidade);
$obFormulario->addTitulo( "Vincular Recurso com ".mb_strtoupper($stTitulo,'UTF-8') );
$obFormulario->addComponente( $obInCodEntidade );
$obFormulario->addComponente( $obInCodOrgao );
$obFormulario->addComponente( $obInCodUnidade );
if ($stAcao == '1') {$obISelectMultiplRecurso2->geraFormulario($obFormulario);}
$obISelectMultiplRecurso->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
