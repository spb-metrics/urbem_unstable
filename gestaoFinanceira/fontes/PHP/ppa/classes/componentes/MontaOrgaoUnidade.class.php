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
* Classe de regra de interface para Orgao Unidade  -  Adaptações para funcionamento em módulos externos a GetãoFinanceira.
* Data de Criação: 27/10/2008

* CONFEDERAÇÃO NACIONAL DOS MUNICÍPIOS

* @author Analista: Heleno Santos
* @author Desenvolvedor: Aldo JEan

    $Id: $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_PPA_NEGOCIO."RPPAOrcamentoDespesa.class.php");
include_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/instancias/montaOrgaoUnidade/JSMontaOrgaoUnidade.js';

class MontaOrgaoUnidade extends Objeto
{
var $stNameMontaOrgaoUnidade;
var $stActionPosteriorMOrgaoUnidade;
var $stActionAnteriorMOrgaoUnidade;
var $stTargetMOrgaoUnidade;
var $stRotuloMOrgaoUnidade;
var $stTitleMontaOrgaoUnidade;
var $stMascaraMOrgaoUnidade;
var $stSelecionadoMOrgaoUnidade;
var $stValueMOrgaoUnidade;
var $inMontaCodOrgaoM;
var $inMontaCodUnidadeM;
var $boIFrameMontaOrgaoUnidade;
var $boNullMontaOrgaoUnidade;
var $boExecutaFrameMOrgaoUnidade;
var $obRDespesaMontaOrgaoUnidade;
var $obRConfiguracaoOrcamento;
var $stAddFunctionMOrgaoUnidade;
var $jsOrgao;

function setName($valor) { $this->stNameMontaOrgaoUnidade           = $valor;                         }
function setRotulo($valor) { $this->stRotuloMOrgaoUnidade         = $valor;                         }
function setMascara($valor) { $this->stMascaraMOrgaoUnidade        = $valor;                         }
function setSelecionado($valor) { $this->stSelecionadoMOrgaoUnidade    = $valor;                         }
function setValue($valor) { $this->stValueMOrgaoUnidade          = $valor;                         }
function setCodOrgao($valor) { $this->inMontaCodOrgaoM       = $valor;                         }
function setCodUnidade($valor) { $this->inMontaCodUnidadeM    = $valor;                          }
function setActionPosterior($valor) {  $this->stActionPosteriorMOrgaoUnidade= $valor.'?'.Sessao::getId(); }
function setActionAnterior($valor) {  $this->stActionAnteriorMOrgaoUnidade = $valor.'?'.Sessao::getId(); }
function setTarget($valor) { $this->stTargetMOrgaoUnidade         = $valor;                         }
function setTitle($valor) { $this->stTitleMontaOrgaoUnidade          = $valor;                         }
function setIFrame($valor) { $this->boIFrameMontaOrgaoUnidade         = $valor;                         }
function setNull($valor) { $this->boNullMontaOrgaoUnidade           = $valor;                         }
function setExecutaFrame($valor) { $this->boExecutaFrameMOrgaoUnidade   = $valor;                         }
function setRDespesa($valor) { $this->obRDespesaMontaOrgaoUnidade       = $valor;                         }
function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor;       }
function getName() { return $this->stNameMontaOrgaoUnidade;                                     }
function getRotulo() { return $this->stRotuloMOrgaoUnidade;                                   }
function getMascara() { return $this->stMascaraMOrgaoUnidade;                                  }
function getSelecionado() { return $this->stSelecionadoMOrgaoUnidade;                              }
function getValue() { return $this->stValueMOrgaoUnidade;                                    }
function getCodOrgao() { return $this->inMontaCodOrgaoM;                                 }
function getCodUnidade() { return $this->inMontaCodUnidadeM;                               }
function getActionPosterior() { return $this->stActionPosteriorMOrgaoUnidade;                          }
function getActionAnterior() { return $this->stActionAnteriorMOrgaoUnidade;                           }
function getTarget() { return $this->stTargetMOrgaoUnidade;                                   }
function getTitle() { return $this->stTitleMontaOrgaoUnidade;                                    }
function getIFrame() { return $this->boIFrameMontaOrgaoUnidade;                                   }
function getNull() { return $this->boNullMontaOrgaoUnidade;                                     }
function getExecutaFrame() { return $this->boExecutaFrameMOrgaoUnidade;                             }
function getRDespesa() { return $this->obRDespesaMontaOrgaoUnidade;                                 }
function getAddFunction() { return $this->stAddFunctionMOrgaoUnidade;            }

function MontaOrgaoUnidade()
{
    $this->setName('stUnidadeOrcamentaria');
    $this->setRotulo('Unidade Orçamentária');
    $this->setActionAnterior('');
    $this->setActionPosterior('');
    $this->setTarget('telaPrincipal');
    $this->setRDespesa(new ROrcamentoDespesa);
    $this->setRConfiguracaoOrcamento(new ROrcamentoConfiguracao);
    $this->setIFrame(false);
    $this->setExecutaFrame(true);
    $stMasc = $this->obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('masc_despesa');
    $arMascDotacao = preg_split( "/[^a-zA-Z0-9]/", $stMasc );
    $stMascaraMOrgaoUnidade     = $arMascDotacao[0].".".$arMascDotacao[1];
    $this->setMascara( $stMascaraMOrgaoUnidade );
    $this->setTitle("Informe o orgão ou unidade.");
}

function geraFormulario(&$obFormulario)
{
    //Monta text com o valor da mascara do SubGrupo
    $obTxtMascMontaOrgaoUnidade = new TextBox;
    $obTxtMascMontaOrgaoUnidade->setName($this->getName());
    $obTxtMascMontaOrgaoUnidade->setRotulo($this->getRotulo());
    $obTxtMascMontaOrgaoUnidade->setNull($this->getNull());
    $obTxtMascMontaOrgaoUnidade->setValue($this->getValue());
    $obTxtMascMontaOrgaoUnidade->setSize(strlen($this->getMascara()));
    $obTxtMascMontaOrgaoUnidade->setMaxLength(strlen($this->getMascara()));
    $obTxtMascMontaOrgaoUnidade->obEvento->setOnFocus("selecionaValorCampo( this );");
    $obTxtMascMontaOrgaoUnidade->obEvento->setOnKeyUp("mascaraDinamico('".$this->getMascara()."', this, event);");
    $obTxtMascMontaOrgaoUnidade->obEvento->setOnChange("buscaOCMontaOrgaoUnidade('buscaValoresUnidade', '../../../../../../gestaoFinanceira/fontes/PHP/ppa/instancias/montaOrgaoUnidade/OCMontaOrgaoUnidade.php', '".$this->getActionPosterior()."', '".$this->getTarget()."', '".Sessao::getId()."');");
    $obTxtMascMontaOrgaoUnidade->obEvento->setOnChange("buscaOCMontaOrgaoUnidade('preencheUnidade', '../../../../../../gestaoFinanceira/fontes/PHP/ppa/instancias/montaOrgaoUnidade/OCMontaOrgaoUnidade.php', '".$this->getActionPosterior()."', '".$this->getTarget()."', '".Sessao::getId()."');");

    //Monta combo para seleção de ORGÃO ORCAMENTARIO
    $this->obRDespesaMontaOrgaoUnidade->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar($rsOrgao, "ORDER BY num_orgao");
    $obCmbOrgao = new Select;
    $obCmbOrgao->setName('inMontaCodOrgaoM');
    $obCmbOrgao->setId('inMontaCodOrgaoM');
    $obCmbOrgao->setValue($this->getCodOrgao());
    $obCmbOrgao->setRotulo('Orgão');
    $obCmbOrgao->setStyle("width: 400px");
    $obCmbOrgao->setNull(true);
    $obCmbOrgao->setCampoId("[cod_orgao]-[num_orgao]-[exercicio]");
    $obCmbOrgao->setCampoDesc("[num_orgao] - [nom_orgao]");
    $obCmbOrgao->addOption("", "Selecione");
    $obCmbOrgao->obEvento->setOnChange("buscaOCMontaOrgaoUnidade('buscaValoresUnidade', '../../../../../../gestaoFinanceira/fontes/PHP/ppa/instancias/montaOrgaoUnidade/OCMontaOrgaoUnidade.php', '".$this->getActionPosterior()."', '".$this->getTarget()."', '".Sessao::getId()."');");
    $obCmbOrgao->preencheCombo($rsOrgao);

    //Monta combo para seleção de UNIDADE ORCAMENTARIA
    $obCmbUnidade = new Select;
    $obCmbUnidade->setName('inMontaCodUnidadeM');
    $obCmbUnidade->setId('inMontaCodUnidadeM');
    $obCmbUnidade->setValue($this->getCodUnidade());
    $obCmbUnidade->setRotulo('Unidade');
    $obCmbUnidade->setStyle("width: 400px");
    $obCmbUnidade->setCampoId("num_unidade");
    $obCmbUnidade->setCampoDesc("[num_orgao].[num_unidade] - [nom_nom_unidade]");
    $obCmbUnidade->addOption("", "Selecione");
    $obCmbUnidade->obEvento->setOnChange("buscaOCMontaOrgaoUnidade('buscaValoresUnidade', '../../../../../../gestaoFinanceira/fontes/PHP/ppa/instancias/montaOrgaoUnidade/OCMontaOrgaoUnidade.php', '".$this->getActionPosterior()."', '".$this->getTarget()."', '".Sessao::getId()."');");
    $obCmbUnidade->setNull(true);

    if ($this->getNull()) {
        $stSimbObrg = '';
    } else {
        $stSimbObrg = '*';
    }

    if ($this->getValue() != '') {
        $this->buscaValoresUnidade();
        $this->preencheUnidade();
    }

    $obFormulario->abreLinha();
    $obFormulario->addRotulo($this->getTitle(), $stSimbObrg.$this->getRotulo(), 3);
    $obFormulario->addCampo($obTxtMascMontaOrgaoUnidade);
    $obFormulario->fechaLinha();

    $obFormulario->abreLinha();
    $obFormulario->addCampo($obCmbOrgao);
    $obFormulario->fechaLinha();

    $obFormulario->abreLinha();
    $obFormulario->addCampo($obCmbUnidade);
    $obFormulario->fechaLinha();
}

function buscaValoresUnidade()
{
    if ($this->getValue() != '') {
        $stUnidadeOrcRequest = $this->getValue();
    } else {
        $stUnidadeOrcRequest = "";
        $stSelecionadoMOrgUniRequest = $_REQUEST['stSelecionadoMOrgaoUnidade'];
        $inMontaCodOrgRequest = $_REQUEST['inMontaCodOrgaoM'];
        $inMontaCodUnidRequest = $_REQUEST["inMontaCodUnidadeM"];
    }
    if ($stSelecionadoMOrgUniRequest == "inMontaCodOrgaoM") {
        $inMontaCodUnidRequest = "";
    }
    if ($inMontaCodOrgRequest != "") {
        $arCodOrgao = explode('-' , $inMontaCodOrgRequest);

        $this->obRDespesaMontaOrgaoUnidade->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arCodOrgao[1]);
        $this->obRDespesaMontaOrgaoUnidade->obROrcamentoUnidadeOrcamentaria->listar($rsUnidade, " ORDER BY num_unidade");

        $arCodUnidade = null;
        if ($rsUnidade->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.inMontaCodUnidadeM,0); \n";
            $js .= "f.inMontaCodUnidadeM.options[0] = new Option('Selecione','');\n";
            $stFlagCombo = '';
            $inSelecionaCombo = '';
            while (!$rsUnidade->eof()) {
                $inMontaCodUnidadeM   = $rsUnidade->getCampo("cod_unidade")." - ".$rsUnidade->getCampo("num_unidade")." - ".$rsUnidade->getCampo("ano_exercicio");
                $stNomUnidade   = $rsUnidade->getCampo("num_unidade")." - ".$rsUnidade->getCampo("nom_unidade");
                $selected       = "";
                if ($inMontaCodUnidadeM == $inMontaCodUnidRequest) {
                    $inSelecionaCombo = $inMontaCodUnidadeM;
                    $selected = "selected";
                }
                $js .= "f.inMontaCodUnidadeM.options[$inContador] = new Option('".$stNomUnidade."','".$inMontaCodUnidadeM."'); \n";
                $inContador++;
                if ($selected == '') {
                    $rsUnidade->proximo();
                } else {
                    $stFlagCombo = $selected;
                    $rsUnidade->proximo();
                }

            }
            $arCodUnidade = explode("-" , $inMontaCodUnidRequest);
            $js .= "f.inMontaCodUnidadeM.value = '".$inSelecionaCombo."'\n;";
        } else {
            $js .= "limpaSelect(f.inMontaCodUnidadeM,0); \n";
            $js .= "f.inMontaCodUnidadeM.options[0] = new Option('Selecione','');\n";
            $js .= "f.inMontaCodUnidadeM.value = ''\n;";
        }
    } else {
        $js .= "limpaSelect(f.inMontaCodUnidadeM,0); \n";
        $js .= "f.inMontaCodUnidadeM.options[0] = new Option('Selecione','');\n";
    }
    //monta mascara(parcial) com os valores JA SELECIONADOS

    $arCodOrgao   = explode("-" , $inMontaCodOrgRequest);
    if ($stSelecionadoMOrgUniRequest == "inMontaCodOrgaoM") {
        $stUnidadeOrcamentaria = $arCodOrgao[1];
    } else {
        $stUnidadeOrcamentaria = "";
        $stUnidadeOrcamentaria = $arCodOrgao[1].".".$arCodUnidade[1];
    }

    $arDotacaoOrcamentaria = preg_split("/[^a-zA-Z0-9]/", $stUnidadeOrcRequest);
    $arDotacaoOrcamentaria[0] = $arCodOrgao[1];
    $arDotacaoOrcamentaria[1] = $arCodUnidade[1];
    $stUnidadeOrcamentaria = "";
    for ($iCount = 2; $iCount <= count($arDotacaoOrcamentaria); $iCount++) {
       $stUnidadeOrcamentaria .= $arDotacaoOrcamentaria[$iCount].".";
    }
    $stUnidadeOrcamentaria = $arDotacaoOrcamentaria[0].".".intval($arDotacaoOrcamentaria[1]).".".$stUnidadeOrcamentaria;
    $stUnidadeOrcamentaria = substr($stUnidadeOrcamentaria, 0, strlen($stUnidadeOrcamentaria) - 1);

    $arMascDotacao = Mascara::validaMascaraDinamica($this->getMascara(), $stUnidadeOrcamentaria);
    $js .= "f.stUnidadeOrcamentaria.value = ''; \n";
    $_REQUEST['stUnidadeOrcamentaria'] = $stUnidadeOrcamentaria;

    $_REQUEST["inMontaCodUnidadeM"] = '';

    //if ($arCodOrgao[1] != '' and $arCodUnidade[1] != '' and $stFlagCombo != '') {
        $js .= "f.stUnidadeOrcamentaria.value = '".$arMascDotacao[1]."'; \n";
        $_REQUEST['stUnidadeOrcamentaria'] = $stUnidadeOrcamentaria;
        $_REQUEST["inMontaCodUnidadeM"] = '';
    //}

    if ($this->getIFrame() == false) {
        SistemaLegado::executaFrameOculto($js);
    } else {
        SistemaLegado::executaiFrameOculto($js);
    }
}  // fim buscaValoresUnidade.

function preencheUnidade()
{
    $stUnidadeOrcRequest = $_REQUEST['stUnidadeOrcamentaria'];
    $inMontaCodOrgRequest = $_REQUEST['inMontaCodOrgaoM'];
    $inMontaCodUnidRequest = $_REQUEST["inMontaCodUnidadeM"];
    if ($this->getValue() != '') {
        $stUnidadeOrcRequest = $this->getValue();
    }
    $arCodOrgaoUnidadeDigitados = explode('.' , $stUnidadeOrcRequest);

    $arOrgaoUnidade = preg_split( "/[^a-zA-Z0-9]/", $stUnidadeOrcRequest );
    foreach ($arOrgaoUnidade as $key => $valor) {
        if ($key == '6') {
            $stMascaraMOrgaoUnidadeRubrica = $this->obRDespesaMontaOrgaoUnidade->obROrcamentoClassificacaoDespesa->recuperaMascara();
            $stMascaraMOrgaoUnidadeRubricaSemPontos = str_replace( '.' , '' , $stMascaraMOrgaoUnidadeRubrica );
            $valor = str_pad( $valor , strlen($stMascaraMOrgaoUnidadeRubricaSemPontos), 0 , STR_PAD_RIGHT );
            $arOrgaoUnidade[6] = $valor;
        }
    }
    for ( $iCount = 0; $iCount <= count($arOrgaoUnidade); $iCount++ ) {
        $stRubricaDesmascarada .= $arOrgaoUnidade[$iCount].".";
    }

    $stRubricaDesmascarada = substr( $stRubricaDesmascarada, 0, strlen($stRubricaDesmascarada) - 1 );
    $arMascDotacao = Mascara::validaMascaraDinamica( $this->getMascara() , $stRubricaDesmascarada );
    if ( strlen( $stUnidadeOrcRequest ) > 0 ) {
        $js .= "f.stUnidadeOrcamentaria.value = '".$arMascDotacao[1]."'; \n";
    } else {
        $js .= "f.stUnidadeOrcamentaria.value = ''; \n";
    }
    //preenche combo do Órgão
    $this->obRDespesaMontaOrgaoUnidade->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao, " ORDER BY exercicio, num_orgao" );
    if ( $rsOrgao->getNumLinhas() > -1 ) {
        $inContador = 1;
        $js .= "limpaSelect(f.inMontaCodOrgaoM,0); \n";
        $js .= "f.inMontaCodOrgaoM.options[0] = new Option('Selecione','');\n";
        $inMontaCodOrgRequestSelecionado = '';
        while ( !$rsOrgao->eof() ) {
            $inCodOrgaoMonta  = $rsOrgao->getCampo("cod_orgao");
            $inNumOrgao  = intval($rsOrgao->getCampo("num_orgao"));
            $stExercicio = $rsOrgao->getCampo("ano_exercicio");
            $stNomOrgao  = $rsOrgao->getCampo("nom_orgao");
            $stCodOrgao  = $inCodOrgaoMonta."-".$inNumOrgao."-".$stExercicio;
            $stNomOrgao  = $inNumOrgao." - ".$stNomOrgao;
            $selected    = "";
            if ( $inNumOrgao == intval($arOrgaoUnidade[0]) ) {
                $selected = "selected";
                $inMontaCodOrgRequestSelecionado = $stCodOrgao;
            }
            $js .= "f.inMontaCodOrgaoM.options[$inContador] = new Option('".$stNomOrgao."','".$stCodOrgao."'); \n";
            $inContador++;
            $rsOrgao->proximo();
        }
        if ($inMontaCodOrgRequestSelecionado != '') {
            $js .= "f.inMontaCodOrgaoM.value = '".$inMontaCodOrgRequestSelecionado."'\n ";
        }
        //preenche combo de Unidade
        $this->obRDespesaMontaOrgaoUnidade->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arOrgaoUnidade[0] );
        $this->obRDespesaMontaOrgaoUnidade->obROrcamentoUnidadeOrcamentaria->listar( $rsUnidade, " ORDER BY num_unidade");
        $stFlagCodUnidade = '';
        if ( $rsUnidade->getNumLinhas() > -1 ) {
            $inContadorUnidade = 1;
            $js .= "limpaSelect(f.inMontaCodUnidadeM,0); \n";
            $js .= "f.inMontaCodUnidadeM.options[0] = new Option('Selecione','');\n";
            $inUnidadeDigitada = intval($arOrgaoUnidade[1]);
            $inMontaCodUnidRequestSelecionado = '';
            while ( !$rsUnidade->eof() ) {
                $inCodUnidadeEncontrada  = $rsUnidade->getCampo("num_unidade");
                $inCodUnidade  = $rsUnidade->getCampo("cod_unidade")." - ".$rsUnidade->getCampo("num_unidade")." - ".$rsUnidade->getCampo("ano_exercicio");
                $stNomUnidade  = $rsUnidade->getCampo("num_unidade")." - ".$rsUnidade->getCampo("nom_unidade");
                $selected      = "";

                if ($inCodUnidadeEncontrada == $inUnidadeDigitada) {
                    $selected = "selected";
                    $stFlagCodUnidade = $selected;
                    $inMontaCodUnidRequestSelecionado = $inCodUnidade;
                    $inMontaCodUnidRequest = '';
                    if ($inUnidadeDigitada == '') {
                        $js .= "f.inMontaCodUnidadeM.value = '';\n";
                        $js .= "f.stUnidadeOrcamentaria.value = ''; \n";
                        sistemaLegado::exibeAviso("Informe o código completo de Orgão ou Unidade!","alerta","alerta");
                        break;
                    }
                } // fim if.

                $js .= "f.inMontaCodUnidadeM.options[$inContadorUnidade] = new Option('".$stNomUnidade."','".$inCodUnidade."'); \n";

                $inContadorUnidade++;
                $rsUnidade->proximo();
            } //fim while
            $rsUnidade->setPrimeiroElemento();
            $js .= "f.inMontaCodUnidadeM.value = '".$inMontaCodUnidRequestSelecionado."';\n";

            if ($rsUnidade->eof() and $stFlagCodUnidade == "") {
                $js .= "f.stUnidadeOrcamentaria.value = ''; \n";
                sistemaLegado::exibeAviso("Não existem Unidades com o número $inUnidadeDigitada para o Órgão informado! ({$arCodOrgaoUnidadeDigitados[0]})","alerta","alerta");
            }
        } else {
            $js .= "f.stUnidadeOrcamentaria.value = ''; \n";
            $js .= "limpaSelect(f.inMontaCodUnidadeM,0); \n";
            $js .= "f.inMontaCodUnidadeM.options[0] = new Option('Selecione','');\n";

            sistemaLegado::exibeAviso("Órgão informado não existe! ({$arCodOrgaoUnidadeDigitados[0]})","alerta","alerta");
        }
    } else {
        $js .= "limpaSelect(f.inMontaCodOrgaoM,0); \n";
        $js .= "f.inMontaCodOrgaoM.options[0] = new Option('Selecione','');\n";
    }

    if ($this->getValue() != '') {
        $js .= "f.stUnidadeOrcamentaria.value = '".$this->getValue()."'; \n";
    }

    if ( $this->getExecutaFrame() == true ) {
        if ( $this->getIFrame() == false ) {
            SistemaLegado::executaFrameOculto($js);
        } else {
            SistemaLegado::executaiFrameOculto($js);
        }
    } else {
        return $js;
    }
} // fim preencheUnidade

/**
    * FALTA DESCRICAO
    * @access Public
*/
function preencheMascara()
{
    if (!$_REQUEST['stUnidadeOrcamentaria']) {
        $arMascDotacao = preg_split( "/[^a-zA-Z0-9]/", $this->getMascara() );
        foreach ($arMascDotacao as $key => $valor) {
            $arMascDotacao[$key] = 0;
        }
    } else {
        $arMascDotacao = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST['stUnidadeOrcamentaria'] );
    }
    $stMascDotacao .= $arMascDotacao[0].".".$arMascDotacao[1];
    $stMascDotacao = substr( $stMascDotacao, 0, strlen($stMascDotacao) - 1 );
    $arMascDotacao = Mascara::validaMascaraDinamica( $this->getMascara(), $stMascDotacao );
    $js .= "f.stUnidadeOrcamentaria.value = '".$arMascDotacao[1]."'; \n";
    SistemaLegado::executaFrameOculto( $js );
} // fim preencheMascara.

}
?>
