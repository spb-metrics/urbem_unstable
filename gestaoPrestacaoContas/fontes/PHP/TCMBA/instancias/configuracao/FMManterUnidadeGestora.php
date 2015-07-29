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
    * Página de Formulario de Vinculo entre o tipo de combustivel do URBEM e a do SIGA
    * Data de Criação: 20/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: FMManterUnidadeGestora.php 62862 2015-06-30 20:37:22Z jean $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once(CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTBAConfiguracaoEntidade.class.php");

$stPrograma = "ManterUnidadeGestora";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obITextSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextSelectEntidade->obTextBox->obEvento->setOnChange("executaFuncaoAjax('BuscaCodigo');");
$obITextSelectEntidade->obSelect ->obEvento->setOnChange ("executaFuncaoAjax('BuscaCodigo');");

$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo          ( "Código da Entidade");
$obTxtCodigo->setName            ( "inCodigo"  );
$obTxtCodigo->setId              ( "inCodigo"  );
$obTxtCodigo->setObrigatorio     ( true        );
$obTxtCodigo->setSize            ( 6           );
$obTxtCodigo->setMaxLength       ( 6           );
$obTxtCodigo->setMinLength       ( 6           );
$obTxtCodigo->setAlfaNumerico    ( false       );

$obLabel = new Label();
$obLabel->setValue("Os arquivos serão agrupados pelo código informado para as entidades");

//Lista de códigos cadastrados para cada entidade
$obPersistente = new TTBAConfiguracaoEntidade();
$obPersistente->recuperaCodigos($rsEntidades,'',' ORDER BY ent.cod_entidade');
$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Códigos de cada Entidade');
$obLista->setRecordSet($rsEntidades);
//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Entidade', 75);
$obLista->addCabecalho('Código UG', 15);
//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_entidade]-[nom_cgm]');
$obLista->commitDado();

$obTxtCodigo = new TextBox();
$obTxtCodigo->setName           ('inCodigo_[cod_entidade]');
$obTxtCodigo->setValue          ('valor');
$obTxtCodigo->setSize           ( 4 );
$obTxtCodigo->setMaxLength      ( 4 );
$obTxtCodigo->setMascara      ('9999');
//$obTxtCodigo->setPreencheComZeros      ('E');
$obTxtCodigo->setInteiro        ( true  );
$obLista->addDadoComponente( $obTxtCodigo , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDadoComponente();

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');
$obLista->montaHTML();
$obSpnCodigos->setValue($obLista->getHTML());

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Parâmetros por Entidade" );
$obFormulario->addSpan              ($obSpnCodigos);
//$obFormulario->addSpan              ($obLabel);
//$obFormulario->addComponente        ( $obITextSelectEntidade );
//$obFormulario->addComponente        ( $obTxtCodigo );

$obFormulario->OK      ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
