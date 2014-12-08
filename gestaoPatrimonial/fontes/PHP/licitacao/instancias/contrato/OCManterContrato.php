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
    * Página Oculto para publicação do contrato
    * Data de Criação   : 10/11/2006

    * @author Desenvolvedor: Leandro André Zis

    * $Id: OCManterContrato.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.22
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include padrão do framework
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoContrato.class.php"            );
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoArquivo.class.php"     );
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumentosAtributos.class.php" );
include_once( CAM_FW_HTML."MontaAtributos.class.php" );
include_once( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$stPrograma = "ManterContrato";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setCodCadastro( 1 );

function montaAtributos($documento)
{
    global $obRCadastroDinamico;

    if ($documento) {
        $obRCadastroDinamico->setPersistenteAtributos ( new TLicitacaoDocumentosAtributos() );

        $obRCadastroDinamico->setChavePersistenteValores( array("cod_documento" => $documento ) );
        $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obFormulario = new Formulario;
        $obMontaAtributos->geraFormulario($obFormulario);
        $obFormulario->montaInnerHtml();
        $stHTML = $obFormulario->getHTML();

        $stJs  = "d.getElementById('spnAtributosDocumento').innerHTML = '".$stHTML."';\n";
    } else {
        $stJs  = "d.getElementById('spnAtributosDocumento').innerHTML = '';\n";
    }

    return $stJs;
}

function buscaDocumentoFornecedor($inCgmFornecedor, $inCodDocumento)
{
    $stNumDoc     = '';
    $stDtValidade = '';
    $stDtEmissao  = '';
    if ($inCgmFornecedor and $inCodDocumento) {
        include_once ( TLIC.'TLicitacaoCertificacaoDocumentos.class.php' );
        $obTLicitacaoCertificacaoDocumentos = new TLicitacaoCertificacaoDocumentos;
        $stFiltro = " and  lcd.cod_documento  = $inCodDocumento
                      and  lcd.cgm_fornecedor = $inCgmFornecedor ";
        $obTLicitacaoCertificacaoDocumentos->recuperaDocumentos( $rsDocumentos, $stFiltro, "order by lcd.dt_validade desc" );

        if ( $rsDocumentos->getCampo ( 'num_documento' ) ) {
            $stNumDoc     = $rsDocumentos->getCampo ( 'num_documento' );
            $stDtValidade = $rsDocumentos->getCampo ( 'dt_validade'   );
            $stDtEmissao  = $rsDocumentos->getCampo ( 'dt_emissao'    );
        }

    }
    $stJs  = "f.stNumDocumento.value ='".$stNumDoc."';";
    $stJs .= "f.stDataValidade.value ='".$stDtValidade."';";
    $stJs .= "f.stDataEmissao.value  ='".$stDtEmissao."';";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "montaAtributos":
    $stJs = montaAtributos( $_REQUEST['inCodDocumento'] );
    $stJs .= buscaDocumentoFornecedor ( $_REQUEST['inCGMContratado']  , $_REQUEST['inCodDocumento'] ) ;
    echo $stJs;
    break;

    case "preencheVigencia":
        include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php");
    $obTNormaDataTermino = new TNormaDataTermino;
    $obTNormaDataTermino->setDado('cod_norma', $_REQUEST['inCodNorma']);
    $obTNormaDataTermino->recuperaPorChave($rsDataTermino);
    $stDataVigencia = $rsDataTermino->getCampo('dt_termino');
    $stJs = " d.getElementById('stDataVigencia').innerHTML = '".$stDataVigencia."';";;
    $stJs.= " d.getElementById('hdnDataVigencia').value = '".$stDataVigencia."';";;
    echo $stJs;
    break;

    case 'carregaValorDocumentosContrato':
    preencheValorContrato();
    buscaDocumentoAssinado();
    break;

    case "sincronizaDataValida":
    sincronizarDataValidaDocumento($_REQUEST['inNumDiasValido'], $_REQUEST['stDataEmissao']);
    break;

    case "sincronizaDiasValidos":
    sincronizaDiasValidosDocumento($_REQUEST['stDataValidade'], $_REQUEST['stDataEmissao']);
    break;

    case "preencheObjeto":
    if ($_REQUEST['inCodModalidade'] != '') {
        include_once ( TLIC."TLicitacaoLicitacao.class.php" );
        $obTLicitacacaoLicitacao = new TLicitacaoLicitacao();
        $obTLicitacacaoLicitacao->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade'] );
        $obTLicitacacaoLicitacao->setDado( 'cod_modalidade' , $_REQUEST['inCodModalidade'] );
        $obTLicitacacaoLicitacao->setDado( 'cod_licitacao', $_REQUEST['inCodLicitacao'] );
        $obTLicitacacaoLicitacao->setDado( 'exercicio' , $_REQUEST['stExercicioLicitacao'] );
        $obTLicitacacaoLicitacao->recuperaObjetoLicitacao( $rsLicitacao );

        if ( $rsLicitacao->getNumLinhas() > 0 ) {
           if ($_REQUEST['inCodLicitacao']) {
               $stJs  = "d.getElementById('stDescObjeto').innerHTML = '".nl2br(str_replace("\r\n", "\n", preg_replace("/(\r\n|\n|\r)/", "",$rsLicitacao->getCampo('descricao'))))."';\n";
               $stJs .= "f.hdnDescObjeto.value = '".nl2br(str_replace("\r\n", "\n", preg_replace("/(\r\n|\n|\r)/", "", $rsLicitacao->getCampo('descricao'))))."';\n";
           }
        } else {
            $stJs  = "d.getElementById('stDescObjeto').innerHTML = '';\n";
            $stJs .= "f.hdnDescObjeto.value = '';\n";
        }
        $stJs = isset($stJs) ? $stJs : "";
        $stJs.= "f.inCGMContratado.selectedIndex =  0;\n";
        $stJs.= "limpaSelect(f.inCGMContratado,1);\n";

        $stFiltro = isset($stFiltro) ? $stFiltro : "";
        $obTLicitacacaoLicitacao->recuperaLicitacaoFornecedores( $rsFornecedores, $stFiltro );

        if ( $rsFornecedores->getNumLinhas() == 1 ) {
        $obTLicitacacaoContrato = new TLicitacaoContrato;
        $obTLicitacacaoContrato->setDado('cod_licitacao', $_REQUEST['inCodLicitacao']);
        $obTLicitacacaoContrato->setDado('cod_modalidade', $_REQUEST['inCodModalidade']);
        $obTLicitacacaoContrato->setDado('cgm_fornecedor', $rsFornecedores->getCampo('cgm_fornecedor'));
        $obTLicitacacaoContrato->setDado('exercicio', Sessao::getExercicio());
        $obTLicitacacaoContrato->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTLicitacacaoContrato->recuperaValorContrato($rsValorContrato);

        $nmValorContrato = $rsValorContrato->getCampo('valor_contrato');
        $nmValorContrato = number_format($nmValorContrato, 2, ',', '.');
        $stJs.= " d.getElementById('nmValorContrato').innerHTML = '".$nmValorContrato."';";
        $stJs.= " d.getElementById('hdnValorContrato').value = '".$nmValorContrato."';";
        $selected = 'selected';
        }
        $selected = isset($selected) ? $selected : "";
        while ( !$rsFornecedores->eof() ) {
        $stJs .= "f.inCGMContratado[".$rsFornecedores->getCorrente()."] = new Option('".$rsFornecedores->getCampo('nom_cgm')."','".$rsFornecedores->getCampo('cgm_fornecedor')."','".$selected."');\n";
        $rsFornecedores->proximo();
        }
    } else {
        $stJs = "d.getElementById('stDescObjeto').innerHTML = '&nbsp;';\n";
        $stJs.= "f.hdnDescObjeto.value = '';\n";
        $stJs.= " d.getElementById('nmValorContrato').innerHTML = '';";
        $stJs.= " d.getElementById('hdnValorContrato').value = '';";
        $stJs.= "f.inCGMContratado.selectedIndex =  0;\n";
        $stJs.= "limpaSelect(f.inCGMContratado,1);\n";
    }

    echo $stJs;
    break;

    //Carrega itens vazios na listagem de documentos de publicacao utilizados no carregamento do Form.
    case 'carregaListaDocumentos' :
    $arDocumentos = Sessao::read('arDocumentos');
    echo montaListaDocumentos($arDocumentos);
    break;

    //Inclui itens na listagem de documentos de publicacao utilizados
    case 'incluirDocumentos':
    include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumento.class.php");
    $obTLicitacaoDocumento = new TLicitacaoDocumento;
    $obTLicitacaoDocumento->setDado('cod_documento', $_REQUEST["inCodDocumento"]);
    $obTLicitacaoDocumento->recuperaPorChave($rsDocumentos);
    $stNomDocumento = $rsDocumentos->getCampo('nom_documento');

    $boDocumentoRepetido = false;
    $arDocumentos = Sessao::read('arDocumentos');
    if (is_array($arDocumentos) == true) {
        foreach ($arDocumentos as $arTEMP) {
            if ($arTEMP['inCodDocumento'] == $_REQUEST["inCodDocumento"]) {
                $boDocumentoRepetido = true ;
                break;
            }
        }
    }

    if (!($boDocumentoRepetido)) {
       $inCount = sizeof($arDocumentos);
       $arDocumentos[$inCount]['id'               ] = $inCount + 1;
       $arDocumentos[$inCount]['boNovo'           ] = true;
       $arDocumentos[$inCount]['inCodDocumento'   ] = $_REQUEST[ "inCodDocumento"];
       $arDocumentos[$inCount]['stNumDocumento'   ] = $_REQUEST[ "stNumDocumento" ];
       $arDocumentos[$inCount]['stNomDocumento'   ] = $stNomDocumento;
       $arDocumentos[$inCount]['dtValidade'       ] = $_REQUEST[ "stDataValidade" ];
       $arDocumentos[$inCount]['dtEmissao'        ] = $_REQUEST[ "stDataEmissao"  ];

    } else {
       echo "alertaAviso('Este documento já consta nesse contrato.','form','erro','".Sessao::getId()."');";
    }

    echo 'limpaFormularioDocumentos();';
    echo 'document.getElementById("inNumDiasValido").value = "";';
    Sessao::write('arDocumentos', $arDocumentos);
    echo montaListaDocumentos( $arDocumentos);
    break;

    //Carrega itens da listagem de documentos de publicacao utilizados em seus determinados campos no Form.
    case 'alteraDocumentos':
    $i = 0;
    $arDocumentos = Sessao::read('arDocumentos');
    foreach ($arDocumentos as $key => $value) {
        if (($key+1) == $_REQUEST['id']) {
        $dataValidade = $arDocumentos[$i]['dtValidade'];
        $dataEmissao = $arDocumentos[$i]['dtEmissao'];

        $js ="f.HdnCodDocumento.value = '".$_REQUEST['id']."';";
        $js.="f.inCodDocumento.value = '".$arDocumentos[$i]['inCodDocumento']."';";
        $js.="f.stNumDocumento.value = '".$arDocumentos[$i]['stNumDocumento']."';";
        $js.="f.stDataValidade.value = '".$arDocumentos[$i]['dtValidade']."';";
        $js.="f.stDataEmissao.value = '".$arDocumentos[$i]['dtEmissao']."';";
        $js.="f.btIncluirDocumentos.disabled = true;";
        $js.="f.btAlterarDocumentos.disabled = false;";
        $js.= "f.stDataValidade.disabled = '';";
        $js.= "f.inNumDiasValido.disabled = '';";
        }

        $i++;
    }

    sincronizaDiasValidosDocumento($dataValidade,$dataEmissao);
    Sessao::write('arDocumentos', $arDocumentos);
    echo $js;
    break;

    //Confirma itens alterados da listagem de documentos de publicacao utilizados
    case "alterarDocumentos":
    $inCount = 0;
    include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumento.class.php");
    $obTLicitacaoDocumento = new TLicitacaoDocumento;
    $obTLicitacaoDocumento->setDado('cod_documento', $_REQUEST["inCodDocumento"]);
    $obTLicitacaoDocumento->recuperaPorChave($rsDocumentos);
    $stNomDocumento = $rsDocumentos->getCampo('nom_documento');
    $arDocumentos = Sessao::read('arDocumentos');

    $boDocumentoRepetido = false;
    foreach ($arDocumentos as $key=>$value) {
        if ($value['inCodDocumento'] == $_REQUEST["inCodDocumento"]) {
        if ($value['id'] != $_REQUEST['HdnCodDocumento']) {
            $boDocumentoRepetido = true;
        }
        }
    }

    if (!$boDocumentoRepetido) {
        foreach ($arDocumentos as $key=>$value) {
        if (($key+1) == $_REQUEST['HdnCodDocumento']) {
            $arDocumentos[$inCount]['id'            ] = $inCount + 1;
            $arDocumentos[$inCount]['boAlterado'    ] = true;
            $arDocumentos[$inCount]['inCodDocumento'] = $_REQUEST[ "inCodDocumento"];
            $arDocumentos[$inCount]['stNumDocumento'] = $_REQUEST[ "stNumDocumento" ];
            $arDocumentos[$inCount]['stNomDocumento'] = $stNomDocumento;
            $arDocumentos[$inCount]['dtValidade'    ] = $_REQUEST[ "stDataValidade" ];
            $arDocumentos[$inCount]['dtEmissao'     ] = $_REQUEST[ "stDataEmissao"  ];
        }
        $inCount++;
    }

    Sessao::write('arDocumentos', $arDocumentos);
    echo 'limpaFormularioDocumentos();';
    echo 'document.getElementById("inNumDiasValido").value = "";';
    $js.= montaListaDocumentos($arDocumentos);
    $js.= "f.btIncluirDocumentos.disabled = false;";
    $js.= "f.btAlterarDocumentos.disabled = true;";
    $js.= "f.stDataValidade.disabled = 'disabled';";
    $js.= "f.inNumDiasValido.disabled = 'disabled';";

    echo $js;

    } else {
        echo "alertaAviso('Este documento já consta nesse contrato.','form','erro','".Sessao::getId()."');";
    }
    break;

    case 'excluirDocumentos':
    $boDocumentoRepetido = false;
    $arTEMP            = array();
    $inCount           = 0;
    $arDocumentos = Sessao::read('arDocumentos');
    foreach ($arDocumentos as $key => $value) {
        if (($key+1) != $_REQUEST['id']) {
            $arTEMP[$inCount]['id'            ] = $inCount + 1;
            $arTEMP[$inCount]['inCodDocumento'] = $value[ "inCodDocumento" ];
            $arTEMP[$inCount]['stNumDocumento'] = $value[ "stNumDocumento" ];
            $arTEMP[$inCount]['stNomDocumento'] = $value[ "stNomDocumento" ];
            $arTEMP[$inCount]['dtValidade'    ] = $value[ "dtValidade"     ];
            $arTEMP[$inCount]['dtEmissao'     ] = $value[ "dtEmissao"      ];
            $inCount++;
        }
    }

    Sessao::write('arDocumentos', $arTEMP);
    echo montaListaDocumentos($arTEMP);
    break;

    //Carrega itens vazios na listagem de aditivos de publicacao utilizados no carregamento do Form.
    case 'carregaListaAditivos' :
    echo montaListaAditivos(Sessao::read('arAditivos'));
    break;

    //Inclui itens na listagem de Aditivos de publicacao utilizados
    case 'incluirAditivos':
    $boAditivoRepetido = false;
    $arAditivos = Sessao::read('arAditivos');
    foreach ($arAditivos as $arTEMP) {
        if ($arTEMP['inCodNorma'] == $_REQUEST["inCodNorma"]) {
            $boAditivoRepetido = true ;
            break;
        }
    }

    if (!($boAditivoRepetido)) {
        $inCount = sizeof($arAditivos);
            $arAditivos[$inCount]['id'               ] = $inCount + 1;
        $arAditivos[$inCount]['inCodNorma'       ] = $_REQUEST[ "inCodNorma"];
        $arAditivos[$inCount]['dtVencimento'     ] = $_REQUEST[ "hdnDataVigencia"];
    } else {
        echo "alertaAviso('Este aditivo já consta nesse contrato.','form','erro','".Sessao::getId()."');";
    }

    echo 'limpaFormularioAditivos();';
    Sessao::write('arAditivos', $arAditivos);
    echo montaListaAditivos( $arAditivos);
    break;

    case 'excluirAditivos':
    $arTEMP            = array();
    $inCount           = 0;
    $arAditivos = Sessao::read('arAditivos');
    foreach ($arAditivos as $key => $value) {
        if (($key+1) != $_REQUEST['id']) {
        $arTEMP[$inCount]['id'               ] = $inCount + 1;
        $arTEMP[$inCount]['inCodNorma'     ] = $value[ "inCodNorma"   ];
        $arTEMP[$inCount]['dtVencimento'   ] = $value[ "dtVencimento"     ];
        $inCount++;
        }
    }

    Sessao::write('arAditivos', $arTEMP);
    echo montaListaAditivos($arTEMP);
    break;

    case 'limparTela':
    Sessao::remove('arDocumentos');
    $stJs  = montaListaDocumentos( array() );
    $stJs .= "frm.inCodLicitacao.options[0].selected = true; \n";
    $stJs .= "frm.inCGMContratado.options[0].selected = true; \n";

    echo $stJs;
    break;

    //Carrega itens vazios na listagem de veiculos de publicacao utilizados no carregamento do Form.
    case 'carregaListaVeiculos' :
    $arValores = Sessao::read('arValores');
    echo montaListaVeiculos($arValores);
    break;

    //Inclui itens na listagem de Aditivos de publicacao utilizados
    case 'incluiAditivos':
    $boAditivoRepetido = false;
    $arAditivos = Sessao::read('arAditivos');

    foreach ($arAditivos as $arTEMP) {
        if ($arTEMP['inCodNorma'] == $_REQUEST["inCodNorma"]) {
        $boAditivoRepetido = true ;
        break;
        }
    }

    if (!($boAditivoRepetido)) {
       $inCount = sizeof($arAditivos);
       $arAditivos[$inCount]['id'           ] = $inCount + 1;
       $arAditivos[$inCount]['inCodNorma'   ] = $_REQUEST[ "inCodNorma"];
       $arAditivos[$inCount]['dtVencimento' ] = $_REQUEST[ "hdnDataVigencia"];
    }

    Sessao::write('arAditivos', $arAditivos);
    break;

    //Inclui itens na listagem de veiculos de publicacao utilizados
    case 'incluirListaVeiculos':
    $arValores = Sessao::read('arValores');
    if ($_REQUEST['inVeiculo'] == '') {
        $stMensagem = 'Preencha o campo Veículo de Publicação!';
    }

    if ($_REQUEST['dtDataPublicacao'] == '') {
        $stMensagem = 'Preencha o campo Data de Publicação!';
    }

    $boPublicacaoRepetida = false;
    if ( is_array( $arValores ) ) {
        foreach ($arValores as $arTEMP) {
        if ($arTEMP['inVeiculo'] == $_REQUEST["inVeiculo"] & $arTEMP['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao']) {
            $boPublicacaoRepetida = true ;
            $stMensagem = "Este veículos de publicação já está na lista.";
        }
        }
    }

    if (!$boPublicacaoRepetida AND !$stMensagem) {
        $inCount = sizeof($arValores);
        $arValores[$inCount]['id'             ] = $inCount + 1;
        $arValores[$inCount]['inVeiculo'      ] = $_REQUEST[ "inVeiculo"                  ];
        $arValores[$inCount]['stVeiculo'      ] = $_REQUEST[ "stNomCgmVeiculoPublicadade" ];
        $arValores[$inCount]['dtDataPublicacao' ] = $_REQUEST[ "dtDataPublicacao"             ];
        $arValores[$inCount]['inNumPublicacao'] = $_REQUEST[ "inNumPublicacao"             ];
        $arValores[$inCount]['stObservacao'   ] = $_REQUEST[ "stObservacao"               ];
        $arValores[$inCount]['inCodLicitacao' ] = $_REQUEST[ "HdnCodLicitacao"            ];
    } else {
        echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    Sessao::write('arValores', $arValores);

    echo montaListaVeiculos( $arValores);
    $js.="$('HdnCodVeiculo').value ='';";
    $js.="$('inVeiculo').value ='';";
    $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
    $js.="$('inNumPublicacao').value ='';";
    $js.="$('stObservacao').value = '';";
    $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
    $js.="$('incluiVeiculo').value = 'Incluir';";
    $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao,stNomCgmVeiculoPublicadade, stObservacao, inCodLicitacao, HdnCodLicitacao\')');";
    echo $js;
    break;

    //Carrega itens da listagem de veiculos de publicacao utilizados em seus determinados campos no Form.
    case 'alterarListaVeiculos':
    $i = 0;

    $arValores = Sessao::read('arValores');
    if ( is_array($arValores)) {
        foreach ($arValores as $key => $value) {
        if (($key+1) == $_REQUEST['id']) {
            $js ="$('HdnCodVeiculo').value                      ='".$_REQUEST['id']."';                                            ";
            $js.="$('inVeiculo').value                          ='".$arValores[$i]['inVeiculo']."';             ";
            $js.="$('dtDataPublicacao').value                   ='".$arValores[$i]['dtDataPublicacao']."';        ";
            $js.="$('inNumPublicacao').value                    ='".$arValores[$i]['inNumPublicacao']."';        ";
            $js.="$('stObservacao').value                       ='".$arValores[$i]['stObservacao']."';          ";
            $js.="$('stNomCgmVeiculoPublicadade').innerHTML='".$arValores[$i]['stVeiculo']."';";
            $js.="$('incluiVeiculo').value    ='Alterar';                                                        ";
            $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'alteradoListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodLicitacao, HdnCodLicitacao, HdnCodVeiculo\')');";
        }
        $i++;
        }
    }

    echo $js;
    break;

    //Confirma itens alterados da listagem de veiculos de publicacao utilizados
    case "alteradoListaVeiculos":
    $inCount = 0;
    $boDotacaoRepetida = false;
    $arValores = Sessao::read('arValores');
    foreach ($arValores as $key=>$value) {
       if ($value['inVeiculo'] == $_REQUEST["inVeiculo"] & $value['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao'] AND ( $key+1 != $_REQUEST['HdnCodVeiculo'] ) ) {
           $boDotacaoRepetida = true ;
           break;
       }
    }

    if (!$boDotacaoRepetida) {
          foreach ($arValores as $key=>$value) {
            if (($key+1) == $_REQUEST['HdnCodVeiculo']) {
            $arValores[$inCount]['id'            ] = $inCount + 1;
            $arValores[$inCount]['inVeiculo'     ] = $_REQUEST[ "inVeiculo"                  ];
            $arValores[$inCount]['stVeiculo'     ] = sistemaLegado::pegaDado('nom_cgm','sw_cgm',' WHERE numcgm = '.$_REQUEST['inVeiculo'].' ');
            $arValores[$inCount]['dtDataPublicacao'] = $_REQUEST[ "dtDataPublicacao"         ];
            $arValores[$inCount]['inNumPublicacao']  = $_REQUEST[ "inNumPublicacao"          ];
            $arValores[$inCount]['stObservacao'  ] = $_REQUEST[ "stObservacao"               ];
        }

        $inCount++;
          }
              Sessao::write('arValores', $arValores);
          $js.=montaListaVeiculos($arValores);
          $js.="$('HdnCodVeiculo').value ='';";
          $js.="$('inVeiculo').value ='';";
          $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
          $js.="$('inNumPublicacao').value ='';";
          $js.="$('stObservacao').value = '';";
          $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
          $js.="$('incluiVeiculo').value = 'Incluir';";
          $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodLicitacao, HdnCodLicitacao\')');";
          echo $js;

    } else {
      echo "alertaAviso('Este item já consta na listagem dessa publicação.','form','erro','".Sessao::getId()."');";
    }
    break;

     //Exclui itens da listagem de veiculos de publicacao utilizados
    case 'excluirListaVeiculos':
    $boDotacaoRepetida = false;
    $arTEMP            = array();
    $inCount           = 0;
    $arValores = Sessao::read('arValores');
    foreach ($arValores as $key => $value) {
        if (($key+1) != $_REQUEST['id']) {
          $arTEMP[$inCount]['id'               ] = $inCount + 1;
          $arTEMP[$inCount]['inVeiculo'        ] = $value[ "inVeiculo"      ];
          $arTEMP[$inCount]['stVeiculo'        ] = $value[ "stVeiculo"      ];
          $arTEMP[$inCount]['dtDataPublicacao' ] = $value[ "dtDataPublicacao" ];
          $arTEMP[$inCount]['inNumPublicacao'  ] = $value[ "inNumPublicacao" ];
          $arTEMP[$inCount]['stObservacao'     ] = $value[ "stObservacao"   ];
          $arTEMP[$inCount]['inCodLicitacao'   ] = $value[ "inCodLicitacao" ];
         $inCount++;
       }
    }

    Sessao::write('arValores', $arTEMP);
    echo montaListaVeiculos($arTEMP);
    break;

    case 'limparVeiculo' :
    $js.="$('HdnCodVeiculo').value ='';";
    $js.="$('inVeiculo').value ='';";
    $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
    $js.="$('inNumPublicacao').value ='';";
    $js.="$('stObservacao').value = '';";
    $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
    $js.="$('incluiVeiculo').value = 'Incluir';";
    $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodLicitacao, HdnCodLicitacao\')');";
    echo $js;
    break;

    //Consulta Temporária enquanto o componente IPopUpNumeroContrato não fica pronto.
    case 'consultaContrato':
    if ($_REQUEST['inContrato']!="") {
        if ($_REQUEST['inCodEntidade']!="") {
        $rsRecordSetVeiculo  = new RecordSet;
        $obLicitacaoContrato = new TLicitacaoContrato();

        $stFiltro = "   AND contrato.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
        $stFiltro.= "   AND contrato.num_contrato = ".$_REQUEST['inContrato']."    \n";
        $stFiltro.= "   AND contrato.exercicio    = '".Sessao::getExercicio()."'   \n";

        $obLicitacaoContrato->recuperaContrato($rsRecordSetVeiculo,$stFiltro);
        if (!($rsRecordSetVeiculo->EOF())) {
            while (!($rsRecordSetVeiculo->EOF())) {
            $codLicitacao = $rsRecordSetVeiculo->getCampo("cod_licitacao");
            $codObjeto    = $rsRecordSetVeiculo->getCampo("cod_objeto");
            $stObjeto     = nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsRecordSetVeiculo->getCampo("descricao"))));
            $codModalidade= $rsRecordSetVeiculo->getCampo("cod_modalidade");

            $js = "d.getElementById('inNroLicitacao').innerHTML = '".$codLicitacao."';             ";
            $js.= "d.getElementById('inNroObjeto')   .innerHTML = '".$codObjeto." - ".$stObjeto."';";

            $js.= "f.HdnCodContrato.value  = '".$_REQUEST['inContrato']."';                        ";
            $js.= "f.HdnCodLicitacao.value = '".$codLicitacao."';                                  ";
            $js.= "f.HdnCodModalidade.value= '".$codModalidade."';                                 ";

            $rsRecordSetVeiculo->proximo();
            }
        } else {
            $js = "f.inContrato.value      = '';                                                      ";

            $js.= "f.HdnCodContrato.value   = '';                                                     ";
            $js.= "f.HdnCodLicitacao.value  = '';                                                     ";
            $js.= "f.HdnCodModalidade.value = '';                                                     ";

            $js.="alertaAviso('Número do Contrato(".$_GET['inContrato'].") não encontrado!.','form','erro','".Sessao::getId()."');";
        }
        } else {
           $js ="f.inContrato.value      = '';                                          ";

           $js.="f.HdnCodContrato.value  = '';                                          ";
           $js.="f.HdnCodLicitacao.value = '';                                          ";
           $js.="f.HdnCodModalidade.value= '';                                          ";

           $js.="alertaAviso('Selecione uma entidade.','form','erro','".Sessao::getId()."');";
        }
    }

    echo $js;

    break;

    //Carrega itens vazios na listagem de veiculos de publicacao utilizados no carregamento do Form.
    case 'carregaListaArquivos' :
    $arArquivos = Sessao::read('arArquivos');
    echo montaListaArquivos($arArquivos);
    break;

    case 'consultarListaArquivo' :
    consultarListaArquivo();
    break;

    case "addArquivo":
    /*$stJs = " var file = '<input type=\'file\' name=\'stArquivo[]\' id=\'stArquivo\' />';";
    $stJs.= " var span = d.getElementById('spnListaArquivos').innerHTML;";
    $stJs.= " d.getElementById('spnListaArquivos').innerHTML = span + file;";*/

    //Campo Observação da Publicação
    $obFileArquivo = new FileBox;
    $obFileArquivo->setId     ( "stArquivo"            );
    $obFileArquivo->setName   ( "stArquivo[]"          );
    $obFileArquivo->setValue  ( ""                     );
    $obFileArquivo->setRotulo ( "Arquivo"              );
    $obFileArquivo->setTitle  ( "Selecione o arquivo." );
    $obFileArquivo->setSize( "50" );

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obFileArquivo);
    $obFormulario->montaInnerHtml();
    $stHTML = $obFormulario->getHTML();
    $stJs.= " var span = d.getElementById('spnListaInputFile').innerHTML;";
    $stJs.= " d.getElementById('spnListaInputFile').innerHTML = span+'".$stHTML."';";

    echo $stJs;
    break;

    //Exclui itens da listagem de veiculos de publicacao utilizados
    case 'excluirListaArquivo':
    $arTEMP            = array();
    $inCount           = 0;
    $arArquivos = Sessao::read('arArquivos');
    foreach ($arArquivos as $key => $value) {
        if (($key+1) != $_REQUEST['id']) {
        $arTEMP[$inCount]['id'          ]  = $inCount + 1;
        $arTEMP[$inCount]['arquivo'     ]  = $value[ "arquivo"     ];
        $arTEMP[$inCount]['nom_arquivo' ]  = $value[ "nom_arquivo" ];
        $arTEMP[$inCount]['num_contrato' ] = $value[ "num_contrato" ];
        $arTEMP[$inCount]['cod_entidade' ] = $value[ "cod_entidade" ];
        $arTEMP[$inCount]['exercicio' ]    = $value[ "exercicio" ];
        $inCount++;
        }
    }

    Sessao::write('arArquivos', $arTEMP);
    echo montaListaArquivos($arTEMP);
    break;
}

function sincronizarDataValidaDocumento($inDiasValidos, $inDataEmissao)
{
    if ($inDataEmissao != "") {

        if ($inDiasValidos != "") {
            $diasValidos = $inDiasValidos;
        } else {
            $diasValidos = 0;
        }

        $arDataEmissao = explode('/',$inDataEmissao);
        //defino data de emissao
        $ano = $arDataEmissao[2];
        $mes = $arDataEmissao[1];
        $dia = $arDataEmissao[0];

        $dataEmissao = mktime(0,0,0,$mes,$dia,$ano);

        $dataValidade = strftime("%d/%m/%Y" , strtotime("+".$diasValidos." days",$dataEmissao));

        $stJs  = "jQuery('#stDataValidade').val('".$dataValidade."');\n";
        $stJs .= "jQuery('#inNumDiasValido').val('".$diasValidos."');\n";
        echo $stJs;
    }
}

function sincronizaDiasValidosDocumento($inDataValidade, $inDataEmissao)
{
    $stJs = "";

    if (strlen($inDataValidade) == 10) {

        if ($inDataValidade != "") {
            $arDataValidade = explode('/',$inDataValidade);
            $dataValidade = $inDataValidade;
        } else {
            $arDataValidade = explode('/',date('d/m/Y'));
            $dataValidade = date('d/m/Y');
        }

         //defino data de validade
        $ano1 = $arDataValidade[2];
        $mes1 = $arDataValidade[1];
        $dia1 = $arDataValidade[0];

        //defino data de emissão
        $arDtEmissao = explode('/',$inDataEmissao);
        $ano2 = $arDtEmissao[2];
        $mes2 = $arDtEmissao[1];
        $dia2 = $arDtEmissao[0];

        //calculo timestam das duas datas
        $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
        $timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

        //diminuo a uma data a outra
        $segundos_diferenca = $timestamp1 - $timestamp2;

        //converto segundos em dias
        $diasValido = $segundos_diferenca / (60 * 60 * 24);

        //obtenho o valor absoluto dos dias (tiro o possível sinal negativo)
        $diasValido = abs($diasValido);

        //tiro os decimais aos dias de diferenca
        $diasValido = floor($diasValido);

        $stJs .= "jQuery('#stDataValidade').val('');\n";
        $stJs .= "jQuery('#stDataValidade').val('".$dataValidade."');\n";
        $stJs .= "jQuery('#inNumDiasValido').val('".$diasValido."');\n";
    } else {
        $stJs .= "jQuery('#stDataValidade').val('');\n";
        $stJs .= "jQuery('#inNumDiasValido').val('');\n";
    }

    echo $stJs;
}

function montaListaDocumentos($arRecordSet , $boExecuta = true)
{
    if (is_array($arRecordSet) ) {
        $rsDocumentos = new RecordSet;
        $rsDocumentos->preenche( $arRecordSet );

    $obLista = new Lista;

    $obLista->setTitulo('Documentos Exigidos');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsDocumentos );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Documento");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Número");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Validade");
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomDocumento" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNumDocumento" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtValidade" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alteraDocumentos();" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirDocumentos();" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        return "d.getElementById('spnListaDocumentos').innerHTML = '".$stHTML."';";
    } else {
        return $stHTML;
    }

    } else {
           return "d.getElementById('spnListaDocumentos').innerHTML = '&nbsp;';";
    }
}

function buscaDocumentoAssinado()
{
    $stNumDoc     = '';
    $stDtValidade = '';
    $stDtEmissao  = '';
    $inCount = 0;
    if ( trim($_REQUEST['inCGMFornecedor']) !="") {
        include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacaoDocumentos.class.php");
        $obTLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos;
        $obTLicitacaoDocumentos->setDado('cod_licitacao', $_REQUEST["inCodLicitacao"]);
        $obTLicitacaoDocumentos->setDado('cod_entidade', $_REQUEST["inCodEntidade"]);
        $obTLicitacaoDocumentos->setDado('exercicio', $_REQUEST["exercicio"]);
        $obTLicitacaoDocumentos->setDado('cod_modalidade', $_REQUEST["inCodModalidade"]);

        $stFiltro = " AND cgm_fornecedor=".$_REQUEST['inCGMFornecedor']."\n";

        $obTLicitacaoDocumentos->recuperaDocumentosLicitacaoFornecedor( $rsDocumentos, $stFiltro, "order by ld.cod_documento desc" );
    }

    $arRsDocumentos = isset($rsDocumentos) ? $rsDocumentos->arElementos : "";
    $arDocumentos = Sessao::read('arDocumentos');

    if (is_array($arRsDocumentos) ) {
    foreach ($arRsDocumentos as $chave => $dados) {
        if (isset($arDocumentos[$chave]['inCodDocumento']) && ($dados['cod_documento'] != $arDocumentos[$chave]['inCodDocumento'])) {
        $stNomDocumento = $dados['nom_documento'];
        $inCodDocumento = $dados['cod_documento'];
        $stDtEmissao = $dados['dt_emissao'];
        $stDtValidade = $dados['dt_validade'];
        $inNumDocumento = $dados['num_documento'];

        $inCount = sizeof($arDocumentos);
        $arDocumentos[$inCount]['id'               ] = $inCount + 1;
        $arDocumentos[$inCount]['boNovo'           ] = true;
        $arDocumentos[$inCount]['inCodDocumento'      ] = $inCodDocumento;
        $arDocumentos[$inCount]['stNumDocumento'   ] = $inNumDocumento;
        $arDocumentos[$inCount]['stNomDocumento'   ] = $stNomDocumento;
        $arDocumentos[$inCount]['dtValidade'       ] = $stDtValidade;
        $arDocumentos[$inCount]['dtEmissao'        ] = $stDtEmissao;
        $inCount++;
        }
       }
    }

    Sessao::write('arDocumentos', $arDocumentos);
    $arrayDocumentos = $arDocumentos;
    echo 'limpaFormularioDocumentos();';
    echo montaListaDocumentos( $arrayDocumentos);
}

function preencheValorContrato()
{
     $stJs = buscaDocumentoFornecedor (  $_REQUEST['inCGMFornecedor'] , $_REQUEST['inCodDocumento'] );
     if ($_REQUEST['inCodLicitacao'] && $_REQUEST['inCGMFornecedor']) {
        $obTLicitacacaoContrato = new TLicitacaoContrato;
        $obTLicitacacaoContrato->setDado('cod_licitacao', $_REQUEST['inCodLicitacao']);
        $obTLicitacacaoContrato->setDado('cod_modalidade', $_REQUEST['inCodModalidade']);
        $obTLicitacacaoContrato->setDado('cgm_fornecedor', $_REQUEST['inCGMFornecedor']);
        $obTLicitacacaoContrato->setDado('exercicio', $_REQUEST['exercicio']);
        $obTLicitacacaoContrato->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTLicitacacaoContrato->recuperaValorContrato($rsValorContrato);

        $nmValorContrato = $rsValorContrato->getCampo('valor_contrato');
        $nmValorContrato = number_format($nmValorContrato, 2, ',', '.');
        $stJs.= " d.getElementById('nmValorContrato').innerHTML = '".$nmValorContrato."';";
        $stJs.= " d.getElementById('hdnValorContrato').value = '".$nmValorContrato."';";

        echo $stJs;
    } else {
        $stJs.= " d.getElementById('nmValorContrato').innerHTML = '';";
        $stJs.= " d.getElementById('hdnValorContrato').value = '';";
        echo $stJs;
   }
}

function montaListaAditivos($arRecordSet , $boExecuta = true)
{
    $rsAditivos = new RecordSet;
    $rsAditivos->preenche( $arRecordSet );

    $obLista = new Lista;

    $obLista->setTitulo('Aditivos');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsAditivos );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Número do Aditivo");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Vencimento");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCodNorma"   );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtVencimento" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirAditivos();" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        return "d.getElementById('spnListaAditivos').innerHTML = '".$stHTML."';";
    } else {
        return $stHTML;
    }
}

function montaListaVeiculos($arRecordSet , $boExecuta = true)
{
    if (is_array($arRecordSet)) {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );

        $table = new Table();
        $table->setRecordset   ( $rsRecordSet  );
        $table->setSummary     ( 'Veículos de Publicação'  );

        $table->Head->addCabecalho( 'Veículo de Publicação' , 40  );
        $table->Head->addCabecalho( 'Data', 8  );
        $table->Head->addCabecalho( 'Número Publicação', 12  );
        $table->Head->addCabecalho( 'Observação'     , 40  );

        $table->Body->addCampo( '[inVeiculo]-[stVeiculo] ' , 'E');
        $table->Body->addCampo( 'dtDataPublicacao', 'C' );
    $table->Body->addCampo( 'inNumPublicacao' );
        $table->Body->addCampo( 'stObservacao'  );

        $table->Body->addAcao( 'alterar' ,  'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )' , array( 'alterarListaVeiculos', 'id' ) );
        $table->Body->addAcao( 'excluir' ,  'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )' , array( 'excluirListaVeiculos', 'id' ) );

        $table->montaHTML( true );

        if ($boExecuta) {
            return "d.getElementById('spnListaVeiculos').innerHTML = '".$table->getHTML()."';";
        } else {
            return $this->getHTML();
        }
    }
}

function montaListaArquivos($arRecordSet, $boExecuta = true)
{
    if (is_array($arRecordSet)) {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );

    $obLista = new Lista();
    $obLista->setRecordset( $rsRecordSet  );
    $obLista->setTitulo('Arquivos Digitais');
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Arquivo");
    $obLista->ultimoCabecalho->setWidth( 50 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ações");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_arquivo" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "CONSULTAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:consultarListaArquivo();" );
    $obLista->ultimaAcao->addCampo("1","arquivo");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirListaArquivo();" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        return "d.getElementById('spnListaArquivos').innerHTML = '".$stHTML."';";
    } else {
        return $stHTML;
    }
    }
}

function consultarListaArquivo()
{
    $pathToSave = CAM_GP_LIC_ANEXOS.'contrato/'.$_REQUEST['arquivo'];
    echo " window.location = '".$pathToSave."'; ";
}
