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
* Página de Formulario de filtro do objeto
* Data de Criação   : 11/10/2006

* @author Analista: Lucas Teixeira Stephanou
* @author Desenvolvedor: Lucas Teixeira Stephanou

* Casos de uso :uc-03.04.07, uc-03.04.05,
*/

/*
$Log$
Revision 1.1  2006/10/11 17:21:12  domluc
p/ Diegon:
   O componente de Contrato gera no formulario que o chama um buscainner e um span, o buscainner somente aceita preenchimento via PopUp, ou seja, não é possivel digitar diretamente o numero do contrato.
   Chamando a popup do buscainner, ele devera poder filtrar por ( em ordem)
1) Número do Contrato ( inteiro)
2) Exercicio ( ref a Contrato) ( componente exercicio)
3) Modalidade ( combo)
4) Codigo da Licitação  ( inteiro )
5) Entidade ( componente)

entao o usuario clica em Ok, e o sistema exibe uma lista correspondente ao filtro informado.
o usuario seleciona um dos contratos na listageme o sistema fecha a popup, retornando ao formulario, onde o sistema preenche o numero do convenio e no span criado pelo componente , exibe as informações recorrentes, que sao:
- exercicio
- modalidade
- licitação
- entidade
- cgm contratado

era isso

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

Sessao::write('link', '');

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm']);

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum']);

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral;
$obPeriodicidade               = new Periodicidade;
$obPeriodicidade->setExercicio ( Sessao::getExercicio());

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                        );
$obFormulario->addHidden     ( $obHdnForm                     );
$obFormulario->addHidden     ( $obHdnCampoNum                 );
$obFormulario->addHidden     ( $obHdnCampoNom                 );
$obFormulario->addHidden     ( $obHdnTipoBusca 	              );
$obFormulario->addTitulo     ( "Dados para filtro"            );
$obFormulario->addHidden     ( $obHdnAcao                     );
$obFormulario->addComponente ( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente ( $obPeriodicidade               );

$obFormulario->OK();
$obFormulario->show();

?>
