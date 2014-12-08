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
    * Filtro
    * Data de Criação: 05/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30908 $
    $Name$
    $Author: alex $
    $Date: 2007-11-12 14:46:27 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterTabelaIRRF";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgList = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";
//DEFINICAO DOS COMPONENTES

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];
$campoNum = $_REQUEST['campoNum'];
$campoNom = $_REQUEST['campoNom'];
$nomForm = $_REQUEST['nomForm'];
$inCodEvento = $_REQUEST['inCodEvento'];
$stDescricao = $_REQUEST['stDescricao'];
$boEventoSistema = ($_REQUEST['boEventoSistema'] == "true") ? "true" : "false";

if(isset($_REQUEST['stNaturezaFiltro']))
    $stNatureza = $_REQUEST['stNaturezaFiltro'];
else
    $stNatureza = $_REQUEST['stNatureza'];

$obHdnNaturezaFiltro =  new Hidden;
$obHdnNaturezaFiltro->setName                   ( "stNaturezaFiltro"                                    );
$obHdnNaturezaFiltro->setValue                  ( $stNatureza                                           );

$stNatureza = explode(",",$stNatureza);

$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                               );

$obHdnCampoNum =  new Hidden;
$obHdnCampoNum->setName                         ( "campoNum"                                            );
$obHdnCampoNum->setValue                        ( $campoNum                                             );

$obHdnCampoNom =  new Hidden;
$obHdnCampoNom->setName                         ( "campoNom"                                            );
$obHdnCampoNom->setValue                        ( $campoNom                                             );

$obHdnNomForm =  new Hidden;
$obHdnNomForm->setName                          ( "nomForm"                                             );
$obHdnNomForm->setValue                         ( $nomForm                                              );

$obHdnEventoSistema =  new Hidden;
$obHdnEventoSistema->setName                    ( "boEventoSistema"                                     );
$obHdnEventoSistema->setValue                   ( $boEventoSistema                                      );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgList                                               );

$obTxtCodigo= new TextBox;
$obTxtCodigo->setRotulo                         ( "Código"                                              );
$obTxtCodigo->setTitle                          ( "Informe o código do evento."                         );
$obTxtCodigo->setName                           ( "inCodEvento"                                         );
$obTxtCodigo->setValue                          ( $inCodEvento                                          );
$obTxtCodigo->setMaxLength                      ( 10                                                    );
$obTxtCodigo->setSize                           ( 10                                                    );
$obTxtCodigo->setInteiro                        ( true                                                  );
$obTxtCodigo->setNull                           ( true                                                  );

$obTxtDescricao= new TextBox;
$obTxtDescricao->setRotulo                      ( "Descrição"                                           );
$obTxtDescricao->setTitle                       ( "Informe a descrição do evento."                      );
$obTxtDescricao->setName                        ( "stDescricao"                                         );
$obTxtDescricao->setValue                       ( $stDescricao                                          );
$obTxtDescricao->setMaxLength                   ( 80                                                    );
$obTxtDescricao->setSize                        ( 40                                                    );
$obTxtDescricao->setNull                        ( true                                                  );

$obRdnNaturezaProventos = new Radio();
$obRdnNaturezaProventos->setName                ( "stNatureza"                                          );
$obRdnNaturezaProventos->setRotulo              ( "Natureza"                                            );
$obRdnNaturezaProventos->setTitle               ( "Selecione a natureza do evento."                     );
$obRdnNaturezaProventos->setLabel               ( "Proventos"                                           );
$obRdnNaturezaProventos->setValue               ( "P"                                                   );
$obRdnNaturezaProventos->setNull                ( false                                                 );
$obRdnNaturezaProventos->setDisabled            ( true                                                  );
if ( in_array("P",$stNatureza) ) {
    $obRdnNaturezaProventos->setDisabled        ( false                                                 );
    if($stNatureza[0] == "P")
        $obRdnNaturezaProventos->setChecked         ( true );
}

$obRdnNaturezaDescontos = new Radio();
$obRdnNaturezaDescontos->setName                ( "stNatureza"                                          );
$obRdnNaturezaDescontos->setRotulo              ( "Natureza"                                            );
$obRdnNaturezaDescontos->setTitle               ( "Selecione a natureza do evento."                     );
$obRdnNaturezaDescontos->setLabel               ( "Descontos"                                           );
$obRdnNaturezaDescontos->setValue               ( "D"                                                   );
$obRdnNaturezaDescontos->setNull                ( false                                                 );
$obRdnNaturezaDescontos->setDisabled            ( true                                                  );
if ( in_array("D",$stNatureza) ) {
    $obRdnNaturezaDescontos->setDisabled        ( false                                                 );
    if($stNatureza[0] == "D")
        $obRdnNaturezaDescontos->setChecked         ( true );
}

$obRdnNaturezaInformativos = new Radio();
$obRdnNaturezaInformativos->setName             ( "stNatureza"                                          );
$obRdnNaturezaInformativos->setRotulo           ( "Natureza"                                            );
$obRdnNaturezaInformativos->setTitle            ( "Selecione a natureza do evento."                     );
$obRdnNaturezaInformativos->setLabel            ( "Informativos"                                        );
$obRdnNaturezaInformativos->setValue            ( "I"                                                   );
$obRdnNaturezaInformativos->setNull             ( false                                                 );
$obRdnNaturezaInformativos->setDisabled         ( true                                                  );
if ( in_array("I",$stNatureza) ) {
    $obRdnNaturezaInformativos->setDisabled     ( false                                                 );
    if($stNatureza[0] == "I")
        $obRdnNaturezaInformativos->setChecked      ( true );
}

$obRdnNaturezaBase = new Radio();
$obRdnNaturezaBase->setName                     ( "stNatureza"                                          );
$obRdnNaturezaBase->setRotulo                   ( "Natureza"                                            );
$obRdnNaturezaBase->setTitle                    ( "Selecione a natureza do evento."                     );
$obRdnNaturezaBase->setLabel                    ( "Base"                                                );
$obRdnNaturezaBase->setValue                    ( "B"                                                   );
$obRdnNaturezaBase->setNull                     ( false                                                 );
$obRdnNaturezaBase->setDisabled                 ( true                                                  );
if ( in_array("B",$stNatureza) or $_GET['boBase'] == 'true' ) {
    $obRdnNaturezaBase->setDisabled             ( false                                                 );
    if($stNatureza[0] == "B")
        $obRdnNaturezaBase->setChecked              ( true );
}

$obBtnCancelar = new Button;
$obBtnCancelar->setName                 ( 'cancelar'                                        );
$obBtnCancelar->setValue                ( 'Cancelar'                                        );
$obBtnCancelar->obEvento->setOnClick    ( "window.close();"                                 );

$obBtnOk = new ok;

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addTitulo                        ( "Dados para o Filtro"                                 );
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
$obFormulario->addHidden                        ( $obHdnCampoNum                                        );
$obFormulario->addHidden                        ( $obHdnCampoNom                                        );
$obFormulario->addHidden                        ( $obHdnNomForm                                         );
$obFormulario->addHidden                        ( $obHdnEventoSistema                                   );
$obFormulario->addComponente                    ( $obTxtCodigo                                          );
$obFormulario->addComponente                    ( $obTxtDescricao                                       );
$obFormulario->addHidden                        ( $obHdnNaturezaFiltro                                  );
$obFormulario->agrupaComponentes                ( array($obRdnNaturezaProventos,$obRdnNaturezaDescontos,$obRdnNaturezaInformativos,$obRdnNaturezaBase));
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnCancelar)                        );
//$obFormulario->Cancelar();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
