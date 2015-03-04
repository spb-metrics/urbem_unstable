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
 * Página de Relatório RGF Anexo1
 * Data de Criação   : 08/10/2007

 * @author Tonismar Régis Bernardo

 * @ignore

 * $Id: OCGeraRGFAnexo1.php 61605 2015-02-12 16:04:02Z diogo.zarpelon $

 * Casos de uso : uc-06.01.20
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$obErro = new Erro();

if (!$_REQUEST['cmbBimestre'] && !$_REQUEST['cmbQuadrimestre'] && !$_REQUEST['cmbSemestre']) {
    $obErro->setDescricao('É preciso selecionar ao menos um '.$_REQUEST['stTipoRelatorio'].'.');
}

$stAno = Sessao::getExercicio();

// verifica se a entidade configurada como Consorcio foi selecionada sozinha ou se há outra entidade junto.
$stEntidade = SistemaLegado::pegaDado("valor","administracao.configuracao","WHERE parametro = 'cod_entidade_consorcio' AND exercicio = '".Sessao::getExercicio()."'");

if ((count($_POST['inCodEntidade']) == 1) && (in_array($stEntidade, $_POST['inCodEntidade'])) ) {
    $preview = new PreviewBirt(6,36,50);
    $preview->setTitulo('Demonstrativo da Despesa com Pessoal Consórcio');
} else {
    if ($stAno > 2012 && $stAno < 2015) {
        $preview = new PreviewBirt(6,36,53);
    } else if($stAno > 2014){
        $preview = new PreviewBirt(6,36,67);
    } else {
        $preview = new PreviewBirt(6,36,1);
    }
    $preview->setTitulo('Demonstrativo da Despesa com Pessoal');
}

if ( (count($_POST['inCodEntidade']) > 1) && (in_array($stEntidade, $_POST['inCodEntidade'] )) ) {
    $obErro->setDescricao("A Entidade setada como Consorcio ".$rsEntidade->getCampo('nom_cgm')." deve ser selecionada sozinha.");
    $boPreview = false;
}

$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

$preview->addParametro( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    while ( !$rsEntidade->eof() ) {
        if ( preg_match("/prefeitura/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
            break;
        }
        $rsEntidade->proximo();
    }
}

if (($_REQUEST['cmbBimestre'] == 6 ) || ($_REQUEST['cmbQuadrimestre'] == 3) || ($_REQUEST['cmbSemestre'] == 2)) {
    $preview->addParametro('show_emp', 'false');
} else {
    $preview->addParametro('show_emp', 'true');
}

$preview->addParametro('percentagem_lim_max', 54);
$preview->addParametro('percentagem_lim_pru', 0.513);

switch ($_REQUEST['stTipoRelatorio']) {
    case 'Quadrimestre':
        $preview->addParametro('periodo', $_REQUEST['cmbQuadrimestre']);
        $numPeriodo = $_REQUEST['cmbQuadrimestre'];
    break;
    case 'Semestre':
        $preview->addParametro('periodo', $_REQUEST['cmbSemestre']);
        $numPeriodo = $_REQUEST['cmbSemestre'];
    break;
    case 'Bimestre':
        $preview->addParametro('periodo', $_REQUEST['cmbBimestre']);
        $numPeriodo = $_REQUEST['cmbBimestre'];
    break;
}

$preview->addParametro('tipo_periodo', $_REQUEST['stTipoRelatorio']);

if($_REQUEST["cmbBimestre"] == '') {
    $inPeriodo = $_REQUEST['cmbQuadrimestre'] != '' ? $_REQUEST['cmbQuadrimestre'] : $_REQUEST['cmbSemestre'];
} else {
    $inPeriodo = $_REQUEST["cmbBimestre"];
}

switch ($inPeriodo) {
    case 1:
        if ($_REQUEST['stTipoRelatorio'] == 'Quadrimestre') {
            $data_fim = '30/04/'.$stAno;
            $data_ini = '01/05/'.($stAno - 1);
        } elseif ($_REQUEST['stTipoRelatorio'] == 'Semestre') {
            $data_fim = '30/06/'.$stAno;
            $data_ini = '01/07/'.($stAno - 1);
        } elseif ($_REQUEST['stTipoRelatorio']  == 'Bimestre'){
            $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*2),2,'0',STR_PAD_LEFT),$stAno);
            $data_ini = "01/03/".($stAno - 1);
        }
        $preview->addParametro( 'exercicio_restos', ($stAno - 1));
    break;

    case 2:
        if ($_REQUEST['stTipoRelatorio'] == 'Quadrimestre') {
            $data_fim = '31/08/'.$stAno;
            $data_ini = '01/09/'.($stAno - 1);
            $preview->addParametro( 'exercicio_restos', ($stAno - 1));
        } elseif ($_REQUEST['stTipoRelatorio'] == 'Semestre') {
            $data_fim = '31/12/'.$stAno;
            $data_ini = '01/01/'.$stAno;
            $preview->addParametro( 'exercicio_restos', $stAno);
        } elseif ($_REQUEST['stTipoRelatorio']  == 'Bimestre'){
            $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*2),2,'0',STR_PAD_LEFT),$stAno);
            $data_ini = "01/05/".($stAno - 1);
            $preview->addParametro( 'exercicio_restos', ($stAno - 1));
        }
   break;

    case 3:
        if ($_REQUEST['stTipoRelatorio']  == 'Bimestre'){
            $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*2),2,'0',STR_PAD_LEFT),$stAno);
            $data_ini = "01/07/".($stAno - 1);
            $preview->addParametro( 'exercicio_restos', ($stAno - 1));
        } else {
            $data_fim = '31/12/'.$stAno;
            $data_ini = '01/01/'.$stAno;
            $preview->addParametro( 'exercicio_restos', $stAno);
        }
       
   break;

   case 4:
        if ($_REQUEST['stTipoRelatorio']  == 'Bimestre'){
            $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*2),2,'0',STR_PAD_LEFT),$stAno);
            $data_ini = "01/09/".($stAno - 1);
        }
        $preview->addParametro( 'exercicio_restos', ($stAno - 1));
   break;

   case 5:
        if ($_REQUEST['stTipoRelatorio']  == 'Bimestre'){
            $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*2),2,'0',STR_PAD_LEFT),$stAno);
            $data_ini = "01/11/".($stAno - 1);
        }
        $preview->addParametro( 'exercicio_restos', ($stAno - 1));
   break;

   case 6:
        if ($_REQUEST['stTipoRelatorio']  == 'Bimestre'){
            $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*2),2,'0',STR_PAD_LEFT),$stAno);
            $data_ini = "01/01/".$stAno;
        }
        $preview->addParametro( 'exercicio_restos', $stAno);
   break;
}

$arMesAno = explode('/',$data_ini);
$inMesAtual = substr($arMesAno[1],1,2);
$stAnoExercicio = $arMesAno[2];
$arMesesExtenso = array('1' => 'Jan',
                        '2' => 'Fev',
                        '3' => 'Mar',
                        '4' => 'Abr',
                        '5' => 'Mai',
                        '6' => 'Jun',
                        '7' => 'Jul',
                        '8' => 'Ago',
                        '9' => 'Set',
                        '10' => 'Out',
                        '11' => 'Nov',
                        '12' => 'Dez');
$inCont = 1;

while ($inCont <= 12) {
    if ($inMesAtual > 12) {
        $inMesAtual = 1;
        $stAnoExercicio = $stAno;
    }

    switch ($inMesAtual) {
        case 1:  $stMesExtenso = $arMesesExtenso[1];  break;
        case 2:  $stMesExtenso = $arMesesExtenso[2];  break;
        case 3:  $stMesExtenso = $arMesesExtenso[3];  break;
        case 4:  $stMesExtenso = $arMesesExtenso[4];  break;
        case 5:  $stMesExtenso = $arMesesExtenso[5];  break;
        case 6:  $stMesExtenso = $arMesesExtenso[6];  break;
        case 7:  $stMesExtenso = $arMesesExtenso[7];  break;
        case 8:  $stMesExtenso = $arMesesExtenso[8];  break;
        case 9:  $stMesExtenso = $arMesesExtenso[9];  break;
        case 10: $stMesExtenso = $arMesesExtenso[10]; break;
        case 11: $stMesExtenso = $arMesesExtenso[11]; break;
        case 12: $stMesExtenso = $arMesesExtenso[12]; break;
    }

    $preview->addParametro('liquidado_mes'.$inCont, $stMesExtenso.'/'.$stAnoExercicio);
    $inMesAtual++;
    $inCont++;
}

$preview->addParametro( 'data_ini', $data_ini );
$preview->addParametro( 'data_fim', $data_fim );

if (preg_match("/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) || (count($_REQUEST['inCodEntidade']) > 1)) {
    $preview->addParametro('poder', 'Executivo');
    $preview->addParametro('limite_maximo', '54%');
    $preview->addParametro('limite_prudencial', '51,3%');
    $preview->addParametro('limite_alerta', '48,6%');
} elseif (preg_match("/camara.*/i", $rsEntidade->getCampo('nom_cgm')) || preg_match("/camara.*/i", $rsEntidade->getCampo('nom_cgm'))) {
    $preview->addParametro('poder', 'Legislativo');
    $preview->addParametro('limite_maximo', '6%');
    $preview->addParametro('limite_prudencial', '5,7%');
    $preview->addParametro('limite_alerta', '5,4%');
} else {
    $preview->addParametro('poder', 'Executivo');
    $preview->addParametro('limite_maximo', '0%');
    $preview->addParametro('limite_prudencial', '0%');
    $preview->addParametro('limite_alerta', '0%');
}

// verificando se foi selecionado Câmara e outra entidade junto
$rsEntidade->setPrimeiroElemento();
if (!$obErro->ocorreu() && (count($_REQUEST['inCodEntidade']) != 1)) {
    while (!$rsEntidade->eof()) {
        if (preg_match("/c[âa]mara/i", $rsEntidade->getCampo('nom_cgm'))) {
            $obErro->setDescricao("Entidade ".$rsEntidade->getCampo('nom_cgm')." deve ser selecionada sozinha.");
            $boPreview = false;
            break;
        }
        $rsEntidade->proximo();
    }
}

#############################Modificações do tce para o novo layout##############################
//adiciona unidade responsável ao relatório
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";

$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario = new TAdministracaoUsuario;

$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);
$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );
$preview->addParametro( 'tipoAnexo', 'anexo1novo');
#################################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));

if (!$obErro->ocorreu()) {
    $preview->preview();
} else {
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=".$_REQUEST['stAcao']."", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
}
