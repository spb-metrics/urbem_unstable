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
    * Formulario para vincular o contrato aos empenhos.
    * Data de Criação: 05/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.37

    $Id: FMManterVinculoEmpenhoContrato.php 64087 2015-12-01 16:10:15Z jean $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$jsOnload = "montaParametrosGET('consultaContratoEmpenho', 'inExercicio,inNumContrato,inCodEntidade');";

Sessao::write('dtContrato'       , $request->get('dtAssinatura')     );
Sessao::write('inCodEntidade'    , $request->get('inCodEntidade')    );
Sessao::write('inExercicio'      , $request->get('inExercicio')      );
Sessao::write('inNumeroContrato' , $request->get('inNumeroContrato') );
Sessao::write('inNumContrato'    , $request->get('inNumContrato')    );
Sessao::write('elementos'        , ''                                );

$stAcao = "incluir";

$stLocation = $pgFilt.'?'.Sessao::getId();

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "inExercicio" );
$obHdnExercicio->setValue( $request->get('inExercicio') );

$obHdnNumContrato = new Hidden;
$obHdnNumContrato->setName ( "inNumContrato" );
$obHdnNumContrato->setValue( $request->get('inNumContrato') );

$obHdnNumeroContrato = new Hidden;
$obHdnNumeroContrato->setName ( "inNumeroContrato" );
$obHdnNumeroContrato->setValue( $request->get('inNumeroContrato') );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $request->get('inCodEntidade') );

$obHdnCgmCredor = new Hidden;
$obHdnCgmCredor->setName ( "cgm_credor" );
$obHdnCgmCredor->setValue( $request->get('cgm_contratado') );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo( "Exercício" );
$obLblExercicio->setValue ( $request->get('inExercicio') );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade" );
$obLblEntidade->setValue ( $request->get('stNomEntidade') );

$obLblContrato = new Label;
$obLblContrato->setRotulo( "Contrato" );
$obLblContrato->setValue ( $request->get('inNumeroContrato')."/".$request->get('inExercicio') );

$obLblDataContrato = new Label;
$obLblDataContrato->setRotulo( "Data do Contrato" );
$obLblDataContrato->setValue ( $request->get('dtAssinatura') );

$obLblCredor = new Label;
$obLblCredor->setRotulo( "Credor" );
$obLblCredor->setValue ( $request->get('stNomCredor') );

$obLblEmpenho = new BuscaInner;
$obLblEmpenho->setRotulo                ( "Empenho"     );
$obLblEmpenho->setId                    ( "stEmpenho"   );
$obLblEmpenho->setValue                 ( $stEmpenho    );
$obLblEmpenho->obCampoCod->setInteiro   ( false         );
$obLblEmpenho->setMostrarDescricao      ( false         );
$obLblEmpenho->obCampoCod->setName      ( "numEmpenho"  );
$obLblEmpenho->obCampoCod->setValue     ( $numEmpenho   );
$obLblEmpenho->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLProcurarEmpenho.php','frm','numEmpenho','stEmpenho','&stNomEntidade=".$request->get('stNomEntidade')."&inCodigoEntidade=".$request->get('inCodEntidade')."&cgmCredor=".$request->get('cgm_contratado')."','".Sessao::getId()."','800','450');");

$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir"     );
$obBtnIncluir->setName              ( "btnIncluir"  );
$obBtnIncluir->setId                ( "btnIncluir"  );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenho','numEmpenho,cgm_credor');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setId                 ( "limpar" );
$obBtnLimpar->setValue              ( "Limpar" );
$obBtnLimpar->obEvento->setOnClick  ( "montaParametrosGET('limpar');" );

$obSpnListaEmpenhos = new Span;
$obSpnListaEmpenhos->setID ( "spnListaEmpenhos" );

$obBtnCancelar = new Button;
$obBtnCancelar->setName             ( "btnClean"                        );
$obBtnCancelar->setValue            ( "Cancelar"                        );
$obBtnCancelar->setTipo             ( "button"                          );
$obBtnCancelar->setDisabled         ( false                             );
$obBtnCancelar->obEvento->setOnClick( "Cancelar('".$stLocation."');"    );

$obBtnOK = new Ok;

$botoesForm = array ( $obBtnOK , $obBtnCancelar );

//Instancia o formulario
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Monta o formulario
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao                                               );
$obFormulario->addHidden( $obHdnCtrl                                               );
$obFormulario->addHidden( $obHdnCgmCredor                                          );
$obFormulario->addHidden( $obHdnExercicio                                          );
$obFormulario->addHidden( $obHdnNumContrato                                        );
$obFormulario->addHidden( $obHdnNumeroContrato                                     );
$obFormulario->addHidden( $obHdnCodEntidade                                        );

$obFormulario->addTitulo( "Dados para Vinculação de Empenhos a um Contrato" );
$obFormulario->addComponente( $obLblExercicio                                      );
$obFormulario->addComponente( $obLblEntidade                                       );
$obFormulario->addComponente( $obLblContrato                                       );
$obFormulario->addComponente( $obLblDataContrato                                   );
$obFormulario->addComponente( $obLblCredor                                         );
$obFormulario->addTitulo( "Empenho" );
$obFormulario->addComponente( $obLblEmpenho                                        );
$obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar ),"","" );
$obFormulario->addSpan      ( $obSpnListaEmpenhos                                  );
$obFormulario->defineBarra  ( $botoesForm                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
