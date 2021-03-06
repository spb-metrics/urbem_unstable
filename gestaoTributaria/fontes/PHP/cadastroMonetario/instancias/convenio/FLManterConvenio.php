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
    * Página de Formulario de Inclusao/Alteracao de Convenio

    * Data de Criação   : 10/10/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FLManterConvenio.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.04

*/

/*
$Log$
Revision 1.11  2006/09/15 14:57:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCarteira.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."ITextBoxSelectBanco.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRMONCarteira = new RMONCarteira;
$obRMONConvenio = new RMONConvenio;
$obRMONConta    = new RMONContaCorrente;
$obITextBoxSelectBanco = new ITextBoxSelectBanco;
$obITextBoxSelectBanco->setNull( true );

$obRMONConta->obRMONAgencia->obRMONBanco->listarBanco($rsBanco);

Sessao::remove('link')  ;
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

$obCodigoBanco = new Hidden;
$obCodigoBanco->setName   ( "inCodBanco" );
$obCodigoBanco->setValue  ( $inCodoBanco  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.05.04" );
$obFormulario->addTitulo     ( "Dados para Filtro" );

$obRMONConvenio->ListarTipoConvenio ( $rsTipoConvenio );

$obCmbTipoConvenio = new Select;
$obCmbTipoConvenio->setRotulo       ('Tipo de Convênio');
$obCmbTipoConvenio->setTitle        ('Tipo de Convênio');
$obCmbTipoConvenio->setName         ( "cmbTipoConvenio"          );
$obCmbTipoConvenio->addOption       ( "", "Selecione"            );
$obCmbTipoConvenio->setValue        ( $_REQUEST['inCodTipoConvenio'] );
$obCmbTipoConvenio->setCampoId      ( "cod_tipo"             );
$obCmbTipoConvenio->setCampoDesc    ( "nom_tipo"             );
$obCmbTipoConvenio->preencheCombo   ( $rsTipoConvenio  );
$obCmbTipoConvenio->setNull         ( true                   );
$obCmbTipoConvenio->setStyle        ( "width: 220px"           );

$obTxtNumConvenio = new TextBox;
$obTxtNumConvenio->setRotulo        ( "Número do Convênio"                   );
$obTxtNumConvenio->setTitle         ( "Número do Convênio" );
$obTxtNumConvenio->setName          ( "inNumConvenio"             );
$obTxtNumConvenio->setValue         ( $inNumConvenio                                 );
$obTxtNumConvenio->setSize          ( 10                                            );
$obTxtNumConvenio->setMaxLength     ( 10                                            );
$obTxtNumConvenio->setNull          ( true                                         );
$obTxtNumConvenio->setInteiro       ( true                                          );

$obTxtBanco = new TextBox;
$obTxtBanco->setRotulo        ( "Banco"                            );
$obTxtBanco->setTitle         ( "Banco ao qual o convênio está estabelecido" );
$obTxtBanco->setName          ( "inNumbanco"                       );
$obTxtBanco->setValue         ( $inNumBanco                        );
$obTxtBanco->setSize          ( 10                                 );
$obTxtBanco->setMaxLength     ( 6                                  );
$obTxtBanco->setNull          ( true                              );
$obTxtBanco->setInteiro       ( true                               );

$obCmbBanco = new Select;
$obCmbBanco->setName          ( "cmbBanco"                   );
$obCmbBanco->addOption        ( "", "Selecione"              );
$obCmbBanco->setValue         ( $_REQUEST['inCodBanco']      );
$obCmbBanco->setCampoId       ( "num_banco"                  );
$obCmbBanco->setCampoDesc     ( "nom_banco"                  );
$obCmbBanco->preencheCombo    ( $rsBanco                     );
$obCmbBanco->setNull          ( true                         );
$obCmbBanco->setStyle         ( "width: 220px"               );
$obCmbBanco->obEvento->setOnChange ( "buscaValor('buscaCodigoBanco');"  );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obCodigoBanco );

//$obFormulario->addComponenteComposto    ( $obTxtBanco,$obCmbBanco  );
$obFormulario->addComponente ( $obITextBoxSelectBanco );
$obFormulario->addComponente ( $obCmbTipoConvenio );
$obFormulario->addComponente ( $obTxtNumConvenio );

$obFormulario->OK();
$obFormulario->show();

$stJs .= 'f.inNumbanco.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
