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
/*
    * Filtro de Cadastro de Aditivo de Contrato TCEMG
    * Data de Criação   : 30/04/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: FLManterAditivoContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';

$stPrograma = "ManterAditivoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];

$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
$obISelectMultiploEntidadeUsuario->setNull( true );
$obISelectMultiploEntidadeUsuario->setTitle("Selecione a entidade");

$obTxtNumeroContrato = new Inteiro;
$obTxtNumeroContrato->setRotulo     ('Número do Contrato'           );
$obTxtNumeroContrato->setName       ('inNumContrato'                );
$obTxtNumeroContrato->setId         ('inNumContrato'                );
$obTxtNumeroContrato->setTitle      ('Informe o número do contrato.');
$obTxtNumeroContrato->setMaxLength  (14);

$obTxtExercicioContrato = new Inteiro;
$obTxtExercicioContrato->setRotulo      ('Exercício do Contrato'            );
$obTxtExercicioContrato->setName        ('stExercicioContrato'              );
$obTxtExercicioContrato->setId          ('stExercicioContrato'              );
$obTxtExercicioContrato->setTitle       ('Informe o exercício do contrato.' );
$obTxtExercicioContrato->setMaxLength   (4);

$obDtContrato = new Data;
$obDtContrato->setRotulo('Data do Contrato' );
$obDtContrato->setName  ('dtContrato'       );
$obDtContrato->setId    ('dtContrato'       );
$obDtContrato->setTitle ('Informe a data da assinatura do contrato.');

if ($stAcao != "incluir") {
    $obTxtNumeroAditivo = new Inteiro;
    $obTxtNumeroAditivo->setRotulo  ('Número do Aditivo');
    $obTxtNumeroAditivo->setName    ('inNumeroAditivo'  );
    $obTxtNumeroAditivo->setId      ('inNumeroAditivo'  );
    $obTxtNumeroAditivo->setTitle   ('Informe o número do aditivo.');

    $obTxtExercicioAditivo = new Inteiro;
    $obTxtExercicioAditivo->setRotulo   ('Exercício do Aditivo' );
    $obTxtExercicioAditivo->setName     ('stExercicioAditivo'   );
    $obTxtExercicioAditivo->setId       ('stExercicioAditivo'   );
    $obTxtExercicioAditivo->setTitle    ('Informe o exercício do aditivo.');
}

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm    );
$obFormulario->addHidden($obHdnCtrl );
$obFormulario->addHidden($obHdnAcao );
$obFormulario->addTitulo    ( "Dados para Filtro"               );
$obFormulario->addComponente( $obISelectMultiploEntidadeUsuario );
$obFormulario->addComponente( $obTxtNumeroContrato              );
$obFormulario->addComponente( $obTxtExercicioContrato           );
$obFormulario->addComponente( $obDtContrato                     );
if ($stAcao != "incluir") {
    $obFormulario->addComponente( $obTxtNumeroAditivo           );
    $obFormulario->addComponente( $obTxtExercicioAditivo        );
}
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
