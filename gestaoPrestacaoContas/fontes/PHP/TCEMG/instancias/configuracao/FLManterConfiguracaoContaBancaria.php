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

    * Página de Filtro de Configuracao Contas Bancarias TCEMG
    * Data de Criação   : 14/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: FLManterConfiguracaoContaBancaria.php 59834 2014-09-15 14:22:10Z lisiane $
    *
    * $Revision: $
    * $Author: $
    * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php");
include_once ( CAM_GF_CONT_COMPONENTES."IPopUpEstruturalPlano.class.php" );
include_once ( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpEstrutural.class.php" );
include_once ( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpContaAnalitica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoContaBancaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

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
    $obRegra = new RContabilidadeLancamentoValor;

    $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade, "E.numcgm" );

    $obRContabilidadePlanoConta = new RContabilidadePlanoConta;
    $obRContabilidadePlanoConta->setExercicio( Sessao::getExercicio() );
    $obRContabilidadePlanoConta->listarGrupos( $rsCodGrupo );

    //****************************************//
    //Define COMPONENTES DO FORMULARIO
    //****************************************//
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction( $pgForm );
    $obForm->setTarget( "telaPrincipal" );

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    //Define o objeto de controle
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( "" );

    $obHdnEntidade = new Hidden;
    $obHdnEntidade->setName ( "inCodEntidade"         );
    $obHdnEntidade->setValue( $_GET['inCodEntidade']  );

    //Define o objeto COMBO para Entidade
    $obCmbEntidade = new Select;
    $obCmbEntidade->setName      ( "inCodEntidade" );
    $obCmbEntidade->setRotulo    ( "Entidade" );

    // Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
    // Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
    if ($rsEntidade->getNumLinhas()>1) {
        $obCmbEntidade->addOption    ( "", "Selecione" );
    }

    $obCmbEntidade->setCampoId   ( "[cod_entidade]" );
    $obCmbEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );
    $obCmbEntidade->preencheCombo( $rsEntidade );
    $obCmbEntidade->setNull      ( false );
    $obCmbEntidade->setTitle     ( 'Selecione uma Entidade' );
    
    $obCmbOrdenacao = new Select;
    $obCmbOrdenacao->setName      ( "inCodOrdenacao" );
    $obCmbOrdenacao->setId        ( "inCodOrdenacao "            );
    $obCmbOrdenacao->setRotulo    ( "Ordenação das contas" );
    $obCmbOrdenacao->addOption    ( "", "Selecione" );
    $obCmbOrdenacao->addOption    ( "1","1 - Código Estrutural " );
    $obCmbOrdenacao->addOption    ( "2","2 - Dados Bancários  " );
    $obCmbOrdenacao->setNull      ( true                  );
    $obCmbOrdenacao->setStyle     ( "width: 220px"        );

    
    //Define o objeto IPopUpEstruturalPlano para Popup da Classificacao Contabil.
    $obIIntervaloPopUpContaAnalitica = new IIntervaloPopUpContaAnalitica;
    $obIIntervaloPopUpContaAnalitica->obIPopUpContaAnaliticaInicial->stTipoBusca = 'somente_contas_analiticas';
    $obIIntervaloPopUpContaAnalitica->obIPopUpContaAnaliticaFinal->stTipoBusca = 'somente_contas_analiticas';

    //Define o objeto TextBox para o Reduzido
    $obTxtReduzido = new TextBox;
    $obTxtReduzido->setName     ( "inCodPlano" );
    $obTxtReduzido->setValue    ( $inCodPlano );
    $obTxtReduzido->setRotulo   ( "Reduzido" );
    $obTxtReduzido->setInteiro  ( true );
    $obTxtReduzido->setSize     ( 20 );
    $obTxtReduzido->setMaxLength( 20 );
    $obTxtReduzido->setNull     ( true );
    $obTxtReduzido->setTitle    ( 'Informe um código reduzido' );

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    $obFormulario->addTitulo         ( "Dados para lançamento de saldos iniciais"  );

    $obFormulario->addComponente     ( $obCmbEntidade            );
    $obFormulario->addComponente     ( $obCmbOrdenacao           );

    $obFormulario->OK();

    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
