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
    * Página de Formulário de Reabertura de Credito Extraordinario
    * Data de Criação   : 10/04/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: melo $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.07
*/

/*
$Log$
Revision 1.6  2007/05/21 18:55:19  melo
Bug #9229#

Revision 1.5  2006/07/28 18:49:08  leandro.zis
corrigido ponto final nos hints

Revision 1.4  2006/07/24 20:19:42  andre.almeida
Bug #6408#

Revision 1.3  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_NEGOCIO. "ROrcamentoSuplementacao.class.php");

$stPrograma = "ReabrirExtraordinario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    include_once( $pgJs );

    $obRegra = new ROrcamentoSuplementacao;
    $obRegra->addDespesaReducao();
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade, "cod_entidade" );
    $obRegra->obRNorma->setExercicio( Sessao::getExercicio() );
    $obRegra->obRNorma->listarDecreto( $rsNorma );

    $stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
    Sessao::remove('arDespesaSuplementar');

    $inCodTipo = 11;

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName( "stCtrl" );
    $obHdnCtrl->setValue( "" );

    $obHdnCodTipo = new Hidden;
    $obHdnCodTipo->setName( "inCodTipo" );
    $obHdnCodTipo->setValue( "$inCodTipo" );

    // Define objeto Select para Codigo da Entidade
    $obCmbCodEntidade = new Select;
    $obCmbCodEntidade->setRotulo    ( "Entidade"         );
    $obCmbCodEntidade->setTitle     ( "Selecione a entidade." );
    $obCmbCodEntidade->setName      ( "inCodEntidade"    );
    $obCmbCodEntidade->setValue     ( $inCodEntidade     );
    $obCmbCodEntidade->setCampoId   ( "cod_entidade"     );
    $obCmbCodEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );
    $obCmbCodEntidade->addOption    ( "", "Selecione"    );
    $obCmbCodEntidade->preencheCombo( $rsEntidade        );
    $obCmbCodEntidade->setNull      ( false              );
    $obCmbCodEntidade->obEvento->setOnChange( "Limpar();" );

    // Define Objeto BuscaInner para Norma
    $obBscNorma = new BuscaInner;
    $obBscNorma->setRotulo ( "Lei/Decreto"   );
    $obBscNorma->setTitle  ( "Selecione uma lei ou decreto." );
    $obBscNorma->setNulL   ( false                    );
    $obBscNorma->setId     ( "stNomTipoNorma"         );
    $obBscNorma->setValue  ( $stNomTipoNorma          );
    $obBscNorma->obCampoCod->setName     ( "inCodNorma" );
    $obBscNorma->obCampoCod->setId       ( "inCodNorma" );
    $obBscNorma->obCampoCod->setSize     ( 10           );
    $obBscNorma->obCampoCod->setMaxLength( 7            );
    $obBscNorma->obCampoCod->setValue    ( $inCodNorma  );
    $obBscNorma->obCampoCod->setAlign    ( "left"       );
    $obBscNorma->obCampoCod->obEvento->setOnBlur("buscaDado('buscaNorma');");
    $obBscNorma->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNorma','stNomTipoNorma','','".Sessao::getId()."','800','550');");

    // Define Objeto Data para Data do lançamento
    $obTxtDtLancamento = new Data;
    $obTxtDtLancamento->setRotulo    ( "Data"        );
    $obTxtDtLancamento->setTitle     ( "Informe a data."        );
    $obTxtDtLancamento->setId        ( "stData"      );
    $obTxtDtLancamento->setName      ( "stData"      );
    $obTxtDtLancamento->setValue     ( ''            );
    //$obTxtDtLancamento->setValue     ( date('d/m/Y') );
    $obTxtDtLancamento->setNull      ( false         );

    // Define Objeto TextArea para Motivo
    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName   ( "stMotivo" );
    $obTxtMotivo->setId     ( "stMotivo" );
    $obTxtMotivo->setValue  ( $stMotivo  );
    $obTxtMotivo->setRotulo ( "Motivo" );
    $obTxtMotivo->setTitle  ( "Informe o motivo." );
    $obTxtMotivo->setNull   ( true );
    $obTxtMotivo->setRows   ( 2 );
    $obTxtMotivo->setCols   ( 100 );

    // Define Objeto Label para Data de publicação
    $obTxtVlTotal = new Numerico;
    $obTxtVlTotal->setRotulo    ( "Valor Total" );
    $obTxtVlTotal->setTitle     ( "Informe o valor total." );
    $obTxtVlTotal->setName      ( "nuVlTotal"   );
    $obTxtVlTotal->setValue     ( $nuVlTotal    );
    $obTxtVlTotal->setSize      ( 23            );
    $obTxtVlTotal->setMaxLength ( 21            );
    $obTxtVlTotal->setNull      ( false         );

    // Define Objeto BuscaInner para Dotação suplementar
    $obBscDespesaSuplementar = new BuscaInner;
    $obBscDespesaSuplementar->setRotulo ( "*Dotação Orçamentária"    );
    $obBscDespesaSuplementar->setTitle  ( "Selecione uma despesa."   );
    $obBscDespesaSuplementar->setNulL   ( true                      );
    $obBscDespesaSuplementar->setId     ( "stNomDespesaSuplementar" );
    $obBscDespesaSuplementar->setValue  ( $stNomDespesaSuplementar  );
    $obBscDespesaSuplementar->obCampoCod->setName ( "inCodDespesaSuplementar"  );
    $obBscDespesaSuplementar->obCampoCod->setSize ( 10 );
    $obBscDespesaSuplementar->obCampoCod->setMaxLength( 5 );
    $obBscDespesaSuplementar->obCampoCod->setValue ( $inCodDespesaSuplementar );
    $obBscDespesaSuplementar->obCampoCod->setId ( "inCodDespesaSuplementar" );
    $obBscDespesaSuplementar->obCampoCod->setAlign ("left");
    $obBscDespesaSuplementar->obCampoCod->obEvento->setOnBlur("buscaDado('buscaDespesa');");
    $obBscDespesaSuplementar->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesaSuplementar','stNomDespesaSuplementar','alteracaoOrcamento&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");

    // Define Objeto Label para Data de publicação
    $obTxtVlSuplementar = new Numerico;
    $obTxtVlSuplementar->setRotulo    ( "*Valor"           );
    $obTxtVlSuplementar->setTitle     ( "Informe o valor da suplementação." );
    $obTxtVlSuplementar->setName      ( "nuVlSuplementar" );
    $obTxtVlSuplementar->setValue     ( $nuVlSuplamentar  );
    $obTxtVlSuplementar->setSize      ( 23                );
    $obTxtVlSuplementar->setMaxLength ( 21                );
    $obTxtVlSuplementar->setNull      ( true              );

    // Define Objeto Button para  Incluir Despesa Suplementar
    $obBtnIncluirSuplementar = new Button;
    $obBtnIncluirSuplementar->setValue( "Incluir" );
    $obBtnIncluirSuplementar->obEvento->setOnClick( "incluirDespesaSuplementar();" );

    // Define Objeto Button para Limpar Despesa Suplementar
    $obBtnLimparSuplementar = new Button;
    $obBtnLimparSuplementar->setValue( "Limpar" );
    $obBtnLimparSuplementar->obEvento->setOnClick( "limparDespesaSuplementar();" );

    // Define objeto Span para lista de Despesas Suplementares
    $obSpnDespesaSuplementar = new Span;
    $obSpnDespesaSuplementar->setId( "spnDespesaSuplementar" );

    //DEFINICAO DOS COMPONENTES
    $obForm = new Form;
    $obForm->setAction                  ( $pgProc );
    $obForm->setTarget                  ( "oculto" );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->setAjuda             ( "UC-02.01.07" );
    $obFormulario->addForm              ( $obForm    );
    $obFormulario->addHidden            ( $obHdnAcao );
    $obFormulario->addHidden            ( $obHdnCtrl );
    $obFormulario->addHidden            ( $obHdnCodTipo );

    $obFormulario->addTitulo            ( "Dados para Reabertura de Crédito Extraordinário" );
    $obFormulario->addComponente        ( $obCmbCodEntidade         );
    $obFormulario->addComponente        ( $obBscNorma               );
    //$obFormulario->addComponente        ( $obCmbLeiDecreto          );
    $obFormulario->addComponente        ( $obTxtDtLancamento        );
    $obFormulario->addComponente        ( $obTxtMotivo              );
    $obFormulario->addComponente        ( $obTxtVlTotal             );
    $obFormulario->addTitulo            ( "Dotações Suplementadas"  );
    $obFormulario->addComponente        ( $obBscDespesaSuplementar  );
    $obFormulario->addComponente        ( $obTxtVlSuplementar       );
    $obFormulario->agrupaComponentes    ( array( $obBtnIncluirSuplementar, $obBtnLimparSuplementar ) );
    $obFormulario->addSpan              ( $obSpnDespesaSuplementar  );

    $obFormulario->ok();

    $obFormulario->show                 ();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
