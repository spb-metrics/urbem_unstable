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
 * Classe de controle - STN - Configuracao
 *
 * @category    Urbem
 * @package     STN
 * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include CAM_FW_COMPONENTES . 'Table/TableTree.class.php';

class CSTNConfiguracao
{
    public $obModel
          ,$arMes;

    /**
     * Metodo construtor, seta o atributo obModel com o que vier na assinatura da funcao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $obModel Classe de Negocio
     *
     * @return void
     */
    public function __construct(&$obModel)
    {
        $this->obModel = $obModel;
        //Monta um array com todos os meses
        $this->arMes = array( '1'  => 'Janeiro'
                             ,'2'  => 'Fevereiro'
                             ,'3'  => 'Março'
                             ,'4'  => 'Abril'
                             ,'5'  => 'Maio'
                             ,'6'  => 'Junho'
                             ,'7'  => 'Julho'
                             ,'8'  => 'Agosto'
                             ,'9'  => 'Setembro'
                             ,'10' => 'Outubro'
                             ,'11' => 'Novembro'
                             ,'12' => 'Dezembro');
    }

    /**
     * Metodo montaFormulario, monta o formulario de vinculo de receita corrente liquida
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array  $arParam Array de dados
     * @param string $stTitle String com o titulo para o formulario
     *
     * @return void
     */
    public function montaForm($arParam)
    {
        if ($arParam['stDataImplantacao'] != '') {
            //Inclui o componente ITextBoxSelectEntidadeGeral
            include CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeGeral.class.php';

            $arData = explode('/',$arParam['stDataImplantacao']);

            //Instancia o componente ITextBoxSelectEntidadeGeral
            $obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
            $obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange("montaParametrosGET('buscaDadosPeriodo');");
            $obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange("montaParametrosGET('buscaDadosPeriodo');");
            $obITextBoxSelectEntidadeGeral->inExercicio = $arData[2];
            $obITextBoxSelectEntidadeGeral->setObrigatorioBarra(true);

            //Preenche o array do periodo com os 12 meses anteriores a data de imp
            for ($i=($arData[1] - 1); $i > ($arData[1] - 14); $i--) {
                if ($i != '0') {
                    $inMes = $i;
                    $inAno = $arData[2];
                    if ($i < 0) {
                        $inMes = 13 + $i;
                        $inAno--;
                    }
                    $arPeriodo[(abs($inMes) . '/' . $inAno)] = ($this->arMes[abs($inMes)] . '/' . $inAno);
                }
            }

            //Instancia um select
            $obSlPeriodo = new Select();
            $obSlPeriodo->setName    ('stPeriodo');
            $obSlPeriodo->setId      ('stPeriodo');
            $obSlPeriodo->setRotulo  ('Período');
            $obSlPeriodo->setTitle   ('Informe o período.');
            $obSlPeriodo->addOption  ('', 'Selecione');
            foreach ($arPeriodo as $stKey => $stValue) {
                $obSlPeriodo->addOption($stKey, $stValue);
            }
            $obSlPeriodo->setObrigatorioBarra(true);

            //Instancia um textbox para o valor do cheque
            $obNumValor = new Numerico();
            $obNumValor->setName            ('flValor');
            $obNumValor->setId              ('flValor');
            $obNumValor->setRotulo          ('Valor');
            $obNumValor->setTitle           ('Informe o valor do período');
            $obNumValor->setObrigatorioBarra(true);
            $obNumValor->setNegativo        (true);

            //Instancia um botao incluir para incluir os dados do formulario na lista
            $obBtnIncluir = new Button();
            $obBtnIncluir->setValue   ('Incluir');
            $obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirValor','flValor,stPeriodo,inCodEntidade');");

            //Instancia um botao para limpar o formulario
            $obBtnLimpar = new Button();
            $obBtnLimpar->setValue   ('Limpar');
            $obBtnLimpar->setId      ('Limpar');
            $obBtnLimpar->obEvento->setOnClick ('limpaFormularioAux();');

            //Instancia um formulario
            $obFormulario = new Formulario();
            $obFormulario->addTitulo      (utf8_decode($arParam['stTitle']));
            $obFormulario->addComponente  ($obITextBoxSelectEntidadeGeral);
            $obFormulario->addComponente  ($obSlPeriodo);
            $obFormulario->addComponente  ($obNumValor);
            $obFormulario->defineBarra    (array($obBtnIncluir,$obBtnLimpar));

            $obFormulario->montaInnerHTML();

            $stJs .= "jq('#spnFormAux').html('" . $obFormulario->getHTML() . "');";
        } else {
            $stJs .= "jq('#spnFormAux').html('');";
        }

        echo $stJs;
    }

    /**
     * Metodo que monta a lista de valores para os periodos
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arPeriodo Array de cheques
     *
     * @return char
     */
    public function buildListaPeriodo($arPeriodo)
    {
        include_once CAM_FW_COMPONENTES . 'Table/Table.class.php';

        $rsPeriodo = new RecordSet();
        $rsPeriodo->preenche      ($arPeriodo);

        if ($rsPeriodo->getNumLinhas() > 0) {
            $rsPeriodo->addFormatacao('valor','NUMERIC_BR');

            $table = new Table ();
            $table->setRecordset($rsPeriodo);
            $table->setSummary  ('Lista de Valores');

            ////$table->setConditional( true , "#efefef" );

            $table->Head->addCabecalho( 'Periodo',          70);
            $table->Head->addCabecalho( 'Valor',        20);

            $table->Body->addCampo('descricao', 'E');
            $table->Body->addCampo('valor'  , 'E');

            $table->Body->addAcao('excluir',
                                  "ajaxJavaScript('OCVincularReceitaCorrenteLiquida.php?Valor&id=%s','excluirValor')",
                                  array('id')
                                 );

            //$table->Foot->addSoma('valor','D');

            $table->montaHTML();

            $stHTML = $table->getHtml();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
        }

        return $stHTML;
    }

    /**
     * Metodo que monta a lista de providencias
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arProvidencia Array de providências
     *
     * @return char
     */
    public function buildListaProvidencia()
    {
        $arProvidencia = Sessao::read('arProvidencia');
        include_once CAM_FW_COMPONENTES . 'Table/Table.class.php';

        $rsProvidencia = new RecordSet();
        $rsProvidencia->preenche      ($arProvidencia);
        $rsProvidencia->addFormatacao('valor', 'NUMERIC_BR');

        if ($rsProvidencia->getNumLinhas() > 0) {
            $rsProvidencia->addFormatacao('valor','NUMERIC_BR');

            $table = new Table ();
            $table->setRecordset($rsProvidencia);
            $table->setSummary  ('Lista de providências');

            ////$table->setConditional( true , "#efefef" );

            $table->Head->addCabecalho('Providência', 85);
            $table->Head->addCabecalho('Valor', 15);

            $table->Body->addCampo('[descricao]', 'E');
            $table->Body->addCampo('[valor]', 'C');

            $table->Body->addAcao('excluir',
                                  "ajaxJavaScript('OCManterRiscosFiscais.php?Valor&cod_providencia=%s','excluirProvidenciaLista')",
                                  array('cod_providencia')
                                 );

            $table->montaHTML();

            $stHTML = $table->getHtml();
            $stHTML = str_replace( "\n" ," " ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
        }

        return $stHTML;
    }

    /**
     * Metodo que monta a lista de valores para os periodos
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arPeriodo Array de cheques
     *
     * @return char
     */
    public function buildListaPeriodoAtuarial($arPeriodo)
    {
        include_once CAM_FW_COMPONENTES . 'Table/Table.class.php';

        $rsPeriodo = new RecordSet();
        $rsPeriodo->preenche      ($arPeriodo);

        if ($rsPeriodo->getNumLinhas() > 0) {
            $rsPeriodo->addFormatacao('vl_receita_previdenciaria', 'NUMERIC_BR');
            $rsPeriodo->addFormatacao('vl_despesa_previdenciaria', 'NUMERIC_BR');
            $rsPeriodo->addFormatacao('vl_saldo_financeiro'      , 'NUMERIC_BR');

            $table = new Table ();
            $table->setRecordset($rsPeriodo);
            $table->setSummary  ('Lista de Valores');

            ////$table->setConditional( true , "#efefef" );

            $table->Head->addCabecalho( 'Exercício'                      , 10);
            $table->Head->addCabecalho( 'Valor da Receita Previdenciária', 20);
            $table->Head->addCabecalho( 'Valor da Despesa Previdenciária', 20);
            $table->Head->addCabecalho( 'Valor do Saldo Financeiro'      , 20);

            $table->Body->addCampo('exercicio'                , 'E');
            $table->Body->addCampo('vl_receita_previdenciaria', 'D');
            $table->Body->addCampo('vl_despesa_previdenciaria', 'D');
            $table->Body->addCampo('vl_saldo_financeiro'      , 'D');

            $table->Body->addAcao('excluir',
                                  "ajaxJavaScript('OCManterParametrosRREO13.php?id=%s','excluirValorAtuarial')",
                                  array('id')
                                 );

            $table->montaHTML();

            $stHTML = $table->getHtml();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
        }

        return $stHTML;
    }

    /**
     * Metodo incluirValor, cadastra na lista o valor para o periodo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirValor($arParam)
    {
        $obErro = new Erro();
        if ($arParam['stPeriodo'] == '') {
            $obErro->setDescricao('Selecione um período');
        } elseif ($arParam['flValor'] == '') {
            $obErro->setDescricao('Preencha o campo valor');
        }
        if (!$obErro->ocorreu()) {
            $arPeriodo = (array) Sessao::read('arPeriodo');
            foreach ($arPeriodo as $arPeriodoAux) {
                if ($arParam['stPeriodo'] == ($arPeriodoAux['mes'] . '/' . $arPeriodoAux['ano']) ) {
                    $obErro->setDescricao('Periodo já está na lista');
                    break;
                }
            }
        }

        $inCount = count($arPeriodo);
        if (!$obErro->ocorreu()) {
            $arData = explode('/',$arParam['stPeriodo']);
            $arPeriodo[$inCount]['id'          ] = $inCount;
            $arPeriodo[$inCount]['cod_entidade'] = $arParam['inCodEntidade'];
            $arPeriodo[$inCount]['mes'         ] = $arData[0];
            $arPeriodo[$inCount]['ano'         ] = $arData[1];
            $arPeriodo[$inCount]['valor'       ] = str_replace(',','.',str_replace('.','',$arParam['flValor'  ]));
            $arPeriodo[$inCount]['descricao'   ] = $this->arMes[$arData[0]] . '/' . $arData[1];

            Sessao::write('arPeriodo',$arPeriodo);

            $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodo($arPeriodo) . "');";
            $stJs .= 'limpaFormularioAux();';
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;

    }

    /**
     * Metodo incluirProvidenciaLista, cadastra na lista uma providencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirProvidenciaLista($arParam)
    {
        $obErro = new Erro();
        if ($arParam['stProvidencia'] == '') {
            $obErro->setDescricao('Campo Providência não pode ser nulo.');
        }
        if ($arParam['flValorProvidencia'] == '' || $arParam['flValorProvidencia'] == '0,00') {
            $obErro->setDescricao('Campo Valor Providência não pode ser nulo ou igual a zero.');
            $stJs .= "jq('#flValorProvidencia').val('');";
            $stJs .= "jq('#flValorProvidencia').focus();";
        }
        if ($arParam['flValor'] == '') {
            $obErro->setDescricao('Informe primeiro o Valor do Risco.');
            $stJs .= "jq('#flValor').focus();";
        } elseif ($arParam['flValor'] == '0,00') {
            $obErro->setDescricao('Valor do Risco não pode ser zero.');
            $stJs .= "jq('#flValor').val('');";
            $stJs .= "jq('#flValor').focus();";
        }
        if (!$obErro->ocorreu()) {
            $arProvidencia = (array) Sessao::read('arProvidencia');
            $flValorProvidencia = str_replace('.', '', $arParam['flValorProvidencia']);
            $flValorProvidencia = str_replace(',', '.', $flValorProvidencia);
            $flValorTMP = $flValorProvidencia;
            $flValorRisco = str_replace('.', '', $arParam['flValor']);
            $flValorRisco = str_replace(',', '.', $flValorRisco);

            if ($arProvidencia) {
                foreach ($arProvidencia AS $arTMP) {
                    $flValorTMP = bcadd($flValorTMP, $arTMP['valor'], 2);
                }
            }

            if ($flValorTMP > $flValorRisco) {
                $obErro->setDescricao('A soma dos Valores das providências não pode ultrapassar o valor do risco.');
                $stJs .= "jq('#flValorProvidencia').val('');";
                $stJs .= "jq('#flValorProvidencia').focus();";
            }
        }

        $inCount = count($arProvidencia);
        if (!$obErro->ocorreu()) {
            $arProvidencia[$inCount]['id'                   ] = $inCount;
            $arProvidencia[$inCount]['cod_providencia'      ] = $inCount+1;
            $stProvidencia = str_replace('\\n', "\n", $arParam['stProvidencia']);
            $stProvidencia = stripslashes($stProvidencia);
            $arProvidencia[$inCount]['descricao'] = $stProvidencia;
            $arProvidencia[$inCount]['valor'] = $flValorProvidencia;

            Sessao::write('arProvidencia',$arProvidencia);

            $this->montaListaProvidencia();
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','form','erro','".Sessao::getId()."');";
            echo $stJs;
        }
    }

    /**
     * Metodo montaListaProvidencia, monta a lista das providências
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function montaListaProvidencia()
    {
        $stJs .= "jq('#spnLista').html('" . $this->buildListaProvidencia() . "');";
        $stJs .= 'limpaFormularioAux();';

        echo $stJs;
    }

    /**
     * Metodo incluirValorAtuarial, cadastra na lista o valor para o periodo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirValorAtuarial($arParam)
    {
        $obErro = new Erro();
        if ($arParam['inCodEntidade'] == '') {

        } elseif ($arParam['stAno'] == '') {
            $obErro->setDescricao('Selecione um exercício');
        } elseif ($arParam['flDespesaPrevidenciaria'] == '') {
            $obErro->setDescricao('Preencha o campo despesa previdenciária');
        } elseif ($arParam['flReceitaPrevidenciaria'] == '') {
            $obErro->setDescricao('Preencha o campo receita previdenciária');
        } elseif ($arParam['flSaldoFinanceiro'] == '') {
            $obErro->setDescricao('Preencha o campo saldo financeiro');
        }
        if (!$obErro->ocorreu()) {
            $arPeriodo = (array) Sessao::read('arPeriodo');
            foreach ($arPeriodo as $arPeriodoAux) {
                if ($arParam['stAno'] == $arPeriodoAux['exercicio']) {
                    $obErro->setDescricao('Este exercício já está na lista');
                    break;
                }
            }
        }

        $inCount = count($arPeriodo);
        if (!$obErro->ocorreu()) {
            $arPeriodo[$inCount]['id'                       ] = $inCount;
            $arPeriodo[$inCount]['exercicio'                ] = $arParam['stAno'];
            $arPeriodo[$inCount]['vl_despesa_previdenciaria'] = str_replace(',','.',str_replace('.','',$arParam['flDespesaPrevidenciaria']));
            $arPeriodo[$inCount]['vl_receita_previdenciaria'] = str_replace(',','.',str_replace('.','',$arParam['flReceitaPrevidenciaria']));
            $arPeriodo[$inCount]['vl_saldo_financeiro'      ] = str_replace(',','.',str_replace('.','',$arParam['flSaldoFinanceiro']));

            Sessao::write('arPeriodo',$arPeriodo);

            $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodoAtuarial($arPeriodo) . "');";
            $stJs .= 'limpaFormularioAux();';
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;

    }

    /**
     * Metodo excluirValor, remove um valor da lista o valor para o periodo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirValor($arParam)
    {
        $arPeriodo = Sessao::read('arPeriodo');
        foreach ($arPeriodo as $arAux) {
            if ($arAux['id'] != $arParam['id']) {
                $arPeriodoNew[] = $arAux;
            }
        }

        Sessao::write('arPeriodo', (array) $arPeriodoNew);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodo((array) $arPeriodoNew) . "');";
        echo $stJs;
    }

    /**
     * Metodo excluirProvidenciaLista, remove um valor da lista de providencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirProvidenciaLista($arParam)
    {
        $arProvidencia = Sessao::read('arProvidencia');
        $inCount = 0;
        foreach ($arProvidencia as $arAux) {
            if ($arAux['cod_providencia'] != $arParam['cod_providencia']) {
                $arProvidenciaNew[$inCount]['cod_providencia'] = $inCount + 1;
                $arProvidenciaNew[$inCount]['descricao'] = $arAux['descricao'];
                $arProvidenciaNew[$inCount]['valor'] = $arAux['valor'];
                $inCount++;
            }
        }

        Sessao::write('arProvidencia', (array) $arProvidenciaNew);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaProvidencia() . "');";
        echo $stJs;
    }

    /**
     * Metodo excluirValor, remove um valor da lista o valor para o periodo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirValorAtuarial($arParam)
    {
        $arPeriodo = Sessao::read('arPeriodo');
        foreach ($arPeriodo as $arAux) {
            if ($arAux['id'] != $arParam['id']) {
                $arPeriodoNew[] = $arAux;
            }
        }

        Sessao::write('arPeriodo', (array) $arPeriodoNew);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodoAtuarial((array) $arPeriodoNew) . "');";
        echo $stJs;
    }

    /**
     * Metodo limpaSessao, remove os dados setados na sessao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function limpaSessao($arParam)
    {
        Sessao::remove('arPeriodo');
        $stJs .= "jq('#spnLista').html('" . array() . "');";
    }

    /**
     * Metodo limpaSessaoProvidencia, remove os dados setados na sessao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function limpaSessaoProvidencia($arParam)
    {
        Sessao::remove('arProvidencia');
        $stJs .= "jq('#spnLista').html('" . array() . "');";
    }

    /**
     * Metodo buscaDadosPeriodo, inclui na sessao e monta a lista
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function buscaDadosPeriodo($arParam)
    {
        $arPeriodo = array();
        if ($arParam['inCodEntidade'] != '') {
            //Pega a data de implantacao e separa os dados
            $arData = explode('/',$arParam['stDataImplantacao']);

            //Seta os dados para o filtro
            $this->obModel->obROrcamentoEntidade->stExercicio      = $arData[2];
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];

            switch ($arParam['stAcao']) {
            case 'incluirRCL':
                $this->obModel->listValorRCL($rsPeriodo);
                break;
            case 'incluirDP':
                $this->obModel->listValorDP($rsPeriodo);
                break;
            }

            if ($rsPeriodo->getNumLinhas() > 0) {
                while (!$rsPeriodo->eof()) {
                    $arPeriodo[] = array(  'id'           => count($arPeriodo)
                                          ,'cod_entidade' => $rsPeriodo->getCampo('cod_entidade')
                                          ,'mes'          => $rsPeriodo->getCampo('mes')
                                          ,'ano'          => $rsPeriodo->getCampo('ano')
                                          ,'valor'        => $rsPeriodo->getCampo('valor')
                                          ,'descricao'    => $this->arMes[$rsPeriodo->getCampo('mes')] . '/' . $rsPeriodo->getCampo('ano')
                                        );

                    $rsPeriodo->proximo();
                }
            }
        }
        Sessao::write('arPeriodo', $arPeriodo);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodo($arPeriodo) . "');";

        echo $stJs;

    }

    /**
     * Metodo buscaDadosPeriodo, inclui na sessao e monta a lista
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function buscaValoresAtuariais($arParam)
    {
        $arPeriodo = array();
        if ($arParam['inCodEntidade'] != '') {
            //Seta os dados para o filtro
            $this->obModel->obROrcamentoEntidade->stExercicio      = Sessao::read('exercicio');
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];

            $this->obModel->listValorRREO13($rsPeriodo);

            if ($rsPeriodo->getNumLinhas() > 0) {
                while (!$rsPeriodo->eof()) {
                    $arPeriodo[] = array(  'id'                               => count($arPeriodo)
                                          ,'exercicio'                        => $rsPeriodo->getCampo('ano')
                                          ,'vl_despesa_previdenciaria'        => $rsPeriodo->getCampo('vl_despesa_previdenciaria')
                                          ,'vl_receita_previdenciaria'        => $rsPeriodo->getCampo('vl_receita_previdenciaria')
                                          ,'vl_saldo_financeiro'              => $rsPeriodo->getCampo('vl_saldo_financeiro')
                                        );

                    $rsPeriodo->proximo();
                }
            }
        }
        Sessao::write('arPeriodo', $arPeriodo);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodoAtuarial($arPeriodo) . "');";

        echo $stJs;
    }

    /**
     * Metodo buscaEntidades
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function buscaEntidades($arParam)
    {
        $this->obModel->obROrcamentoEntidade->stExercicio = $arParam['stExercicio'];
        $stOrder = " cod_entidade ";
        $this->obModel->obROrcamentoEntidade->listar($rsEntidades, $stOrder);
        if ($arParam['stExercicio'] != '') {
            $stJs = "limpaSelect(f.inCodEntidadeDisponivel,0);";
            $stJs = "limpaSelect(f.inCodEntidade,0);";
            if ($rsEntidades->getNumLinhas() > -1) {
                $inContador = 0;
                while (!$rsEntidades->EOF()) {
                    $inCodEntidade = $rsEntidades->getCampo('cod_entidade');
                    $stNomEntidade = $rsEntidades->getCampo('nom_cgm');

                    $stJs .= "f.inCodEntidadeDisponivel.options[$inContador] = new Option('".$stNomEntidade."','".$inCodEntidade."'); \n";
                    $inContador++;
                    $rsEntidades->proximo();
                    $stJs .= "jq('#inCodEntidade').focus();";
                }

            } else {
                $stJs .= "jq('#stExercicio').val('');";
                $stJs .= "alertaAviso('Não existe entidade cadastrada para este exercício.','form','erro','".Sessao::getId()."');";
                $stJs .= "jq('#stExercicio').focus();";
            }
        }

        if ($stJs) {
            echo $stJs;
        }
    }

    /**
     * Metodo verificaDataImplantacao, verifica se a rotina ja foi usada
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function verificaDataImplantacao($arParam)
    {

        $this->obModel->recuperaDataImplantacao($stDataImplantacao);

        if ($stDataImplantacao != '') {
            switch ($arParam['stAcao']) {
            case 'incluirRCL':
                $pgOcul = 'OCVincularReceitaCorrenteLiquida.php';
                $stTitle = 'Dados da Receita Corrente Líquida';
                break;
            case 'incluirDP':
                $pgOcul = 'OCVincularDespesaPessoal.php';
                $stTitle = 'Dados da Despesa Pessoal';
                break;
            }

            //Se existir, seta a data de implantacao e deixa ela como readonly
            $stJs .= "jq('#stDataImplantacao').val('" . $stDataImplantacao . "');";
            $stJs .= "jq('#stDataImplantacao').attr('readonly', 'readonly');";

            $stJs .= "ajaxJavaScript('" . $pgOcul . "?stDataImplantacao='+this.value+'&stTitle=" . utf8_encode($stTitle) . "','montaForm');";
        }

        echo $stJs;
    }

    /**
     * Metodo incluirRCL, adiciona os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirRCL($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a entidade');
        }

        if (!$obErro->ocorreu()) {
            $arPeriodo = Sessao::read('arPeriodo');

            $this->obModel->stDataImplantacao = $arParam['stDataImplantacao'];
            $obErro = $this->obModel->incluirDataImplantacao(false, $boTransacao);

            $arData = explode('/', $arParam['stDataImplantacao']);

            //Lista os periodos cadastrados no banco
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obROrcamentoEntidade->stExercicio      = $arData[2];
            $this->obModel->listValorRCL($rsPeriodo);

            while (!$rsPeriodo->eof()) {
                $arPeriodoDB[$rsPeriodo->getCampo('mes') . '-' . $rsPeriodo->getCampo('ano') . '-' . $rsPeriodo->getCampo('valor')] = true;
                $rsPeriodo->proximo();
            }

            //Inclui os periodos que nao existem na basa
            foreach ((array) $arPeriodo as $arAux) {
                if (!isset($arPeriodoDB[$arAux['mes'] . '-' . $arAux['ano'] . '-' . $arAux['valor']])) {
                    $this->obModel->inMes                                  = $arAux['mes'];
                    $this->obModel->inAno                                  = $arAux['ano'];
                    $this->obModel->flValor                                = $arAux['valor'];

                    $obErro = $this->obModel->vincularReceitaCorrenteLiquida(false, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }

                foreach ((array) $arPeriodoDB as $arDelete => $boValor) {
                    list($inMes, $inAno, $flValor) = explode('-', $arDelete);
                    if ($inMes . '-' . $inAno == $arAux['mes'] . '-' . $arAux['ano']) {
                        unset($arPeriodoDB[$inMes . '-' . $inAno . '-' . $flValor]);
                    }
                }
            }

            //Deleta os que foram removidos
            foreach ((array) $arPeriodoDB as $arAux => $boValor) {
                list($inMes, $inAno) = explode('-', $arAux);
                $this->obModel->inMes                                  = $inMes;
                $this->obModel->inAno                                  = $inAno;

                $obErro = $this->obModel->excluirReceitaCorrenteLiquida(false, $boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso('FMVincularReceitaCorrenteLiquida.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Receita Corrente Líquida vinculada com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }
        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo incluirDP, adiciona os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirDP($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a entidade');
        }

        if (!$obErro->ocorreu()) {
            $arPeriodo = Sessao::read('arPeriodo');

            $this->obModel->stDataImplantacao = $arParam['stDataImplantacao'];
            $obErro = $this->obModel->incluirDataImplantacao(false, $boTransacao);

            $arData = explode('/', $arParam['stDataImplantacao']);

            //Lista os periodos cadastrados no banco
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obROrcamentoEntidade->stExercicio      = $arData[2];
            $this->obModel->listValorDP($rsPeriodo);

            while (!$rsPeriodo->eof()) {
                $arPeriodoDB[$rsPeriodo->getCampo('mes') . '-' . $rsPeriodo->getCampo('ano') . '-' . $rsPeriodo->getCampo('valor')] = true;
                $rsPeriodo->proximo();
            }

            //Inclui os periodos que nao existem na basa
            foreach ((array) $arPeriodo as $arAux) {
                if (!isset($arPeriodoDB[$arAux['mes'] . '-' . $arAux['ano'] . '-' . $arAux['valor']])) {
                    $this->obModel->inMes                                  = $arAux['mes'];
                    $this->obModel->inAno                                  = $arAux['ano'];
                    $this->obModel->flValor                                = $arAux['valor'];

                    $obErro = $this->obModel->vincularDespesaPessoal(false, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                    //unset($arPeriodoDB[$arAux['mes'] . '-' . $arAux['ano'] . '-' . $arAux['valor']]);
                }

                foreach ((array) $arPeriodoDB as $arDelete => $boValor) {
                    list($inMes, $inAno, $flValor) = explode('-', $arDelete);
                    if ($inMes . '-' . $inAno == $arAux['mes'] . '-' . $arAux['ano']) {
                        unset($arPeriodoDB[$inMes . '-' . $inAno . '-' . $flValor]);
                    }
                }
            }

            //Deleta os que foram removidos
            foreach ((array) $arPeriodoDB as $arAux => $boValor) {
                list($inMes, $inAno, $flValor) = explode('-', $arAux);
                $this->obModel->inMes                                  = $inMes;
                $this->obModel->inAno                                  = $inAno;

                $obErro = $this->obModel->excluirDespesaPessoal(false, $boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso('FMVincularDespesaPessoal.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Despesa Pessoal vinculada com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo incluirRREO13, adiciona os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirRREO13($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a entidade');
        }

        if (!$obErro->ocorreu()) {
            $arPeriodo = Sessao::read('arPeriodo');

            //Lista os periodos cadastrados no banco
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obROrcamentoEntidade->stExercicio      = Sessao::read('exercicio');
            $this->obModel->listValorRREO13($rsPeriodo);

            while (!$rsPeriodo->eof()) {
                $stChave = $rsPeriodo->getCampo('ano') . '-' . $rsPeriodo->getCampo('vl_receita_previdenciaria') . '-';
                $stChave.= $rsPeriodo->getCampo('vl_despesa_previdenciaria') . '-' . $rsPeriodo->getCampo('vl_saldo_financeiro');
                $arPeriodoDB[$stChave] = true;
                $rsPeriodo->proximo();
            }

            //Inclui os periodos que nao existem na basa
            foreach ((array) $arPeriodo as $arAux) {
                $stChave = $arAux['exercicio'] . '-' . $arAux['vl_receita_previdenciaria'] . '-';
                $stChave.= $arAux['vl_despesa_previdenciaria'] . '-' . $arAux['vl_saldo_financeiro'];
                if (!isset($arPeriodoDB[$stChave])) {
                    $this->obModel->inAno                                  = $arAux['exercicio'];
                    $this->obModel->flReceitaPrevidenciaria                = $arAux['vl_receita_previdenciaria'];
                    $this->obModel->flDespesaPrevidenciaria                = $arAux['vl_despesa_previdenciaria'];
                    $this->obModel->flSaldoFinanceiro                      = $arAux['vl_saldo_financeiro'];

                    $obErro = $this->obModel->vincularParametrosRREO13(false, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }

                foreach ((array) $arPeriodoDB as $arDelete => $boValor) {
                    list($inAno, $flVlReceita, $flVlDespesa, $flVlSaldo) = explode('-', $arDelete);
                    if ($inAno == $arAux['exercicio']) {
                        unset($arPeriodoDB[$inAno . '-' . $flVlReceita . '-' . $flVlDespesa . '-' . $flVlSaldo]);
                    }
                }
            }

            //Deleta os que foram removidos
            foreach ((array) $arPeriodoDB as $arAux => $boValor) {
                list($inAno, $flVlReceita, $flVlDespesa, $flVlSaldo) = explode('-', $arAux);
                $this->obModel->inAno                                  = $inAno;

                $obErro = $this->obModel->excluirParametrosRREO13(false, $boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso('FMManterParametrosRREO13.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Parametros vinculados com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo incluirDemonstrativo, adiciona os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirDemonstrativo($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a Entidade');
        }

        if ($arParam['stDescricaoRisco'] == '') {
            $obErro->setDescricao('Informe a Descrição');
        }

        if ($arParam['flValor'] == '') {
            $obErro->setDescricao('Informe o Valor');
        }

        if ($arParam['stExercicio'] == '') {
            $obErro->setDescricao('Informe o Exercício');
        }

        if (!$obErro->ocorreu()) {
            $this->obModel->stExercicio = $arParam['stExercicio'];
            $this->obModel->recuperaPPAExercicio($rsPPA);
            if ($rsPPA->getNumLinhas() < 1) {
                $obErro->setDescricao('Não existe um PPA para o exercício informado.');
            }
        }

        if (!$obErro->ocorreu()) {
            $arProvidencia = Sessao::read('arProvidencia');
            $flValorTMP = 0;
            $flValorRisco = str_replace('.', '', $arParam['flValor']);
            $flValorRisco = str_replace(',', '.', $flValorRisco);
            if (!empty($arProvidencia)) {
                foreach ($arProvidencia as $arTMP) {
                    $flValorTMP = bcadd($flValorTMP, $arTMP['valor'], 2);
                }
                if ($flValorTMP != $flValorRisco) {
                    $obErro->setDescricao('A soma do valor das providÊncias deve ser igual ao valor do risco fiscal.');
                }
            } else {
                $obErro->setDescricao('Deve haver pelo menos uma providência relacionada ao risco fiscal');
            }
        }

        if (!is_array($arParam['inCodEntidade'])) {
            $arParam['inCodEntidade'] = (array) $arParam['inCodEntidade'];
        }

        if (!$obErro->ocorreu()) {
            foreach ($arParam['inCodEntidade'] as $inCodEntidade) {
                $this->obModel->stDescricao = $arParam['stDescricaoRisco'];
                $this->obModel->flValor = $arParam['flValor'];
                $this->obModel->stExercicio = $arParam['stExercicio'];
                $this->obModel->inCodEntidade = $inCodEntidade;
                $this->obModel->inCodIdentificador = $arParam['inCodIdentificador'];
                $obErro = $this->obModel->incluirRiscosFiscais(false, $boTransacao);

                if (!$obErro->ocorreu()) {
                    //Inclui as Providencias que nao existem na base
                    foreach ($arProvidencia as $arAux) {
                        $this->obModel->inCodProvidencia   = $arAux['cod_providencia'];
                        $this->obModel->stDescricao        = $arAux['descricao'];
                        $this->obModel->flValorProvidencia = $arAux['valor'];

                        $obErro = $this->obModel->incluirProvidencias(false, $boTransacao);
                        if ($obErro->ocorreu()) {
                            break;
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                $arParam['stAcao'] = 'incluir';
                SistemaLegado::alertaAviso('FMManterRiscosFiscais.php?stAcao='.$arParam['stAcao'], $this->obModel->inCodRisco, $arParam['stAcao'], 'aviso', Sessao::getId(), '../');
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo alterarDemonstrativo, altera os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function alterarDemonstrativo($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a Entidade');
        }

        if ($arParam['stDescricaoRisco'] == '') {
            $obErro->setDescricao('Informe a Descrição');
        }

        if ($arParam['flValor'] == '') {
            $obErro->setDescricao('Informe o Valor');
        }

        if ($arParam['stExercicio'] == '') {
            $obErro->setDescricao('Informe o Exercicio');
        }

        if (!$obErro->ocorreu()) {
            $this->obModel->stExercicio = $arParam['stExercicio'];
            $this->obModel->recuperaPPAExercicio($rsPPA);
            if ($rsPPA->getNumLinhas() < 1) {
                $obErro->setDescricao('Não existe um PPA para o exercício informado.');
            }
        }

        if (!$obErro->ocorreu()) {
            $arProvidencia = Sessao::read('arProvidencia');
            $flValorTMP = 0;
            $flValorRisco = str_replace('.', '', $arParam['flValor']);
            $flValorRisco = str_replace(',', '.', $flValorRisco);
            foreach ($arProvidencia AS $arTMP) {
                $flValorTMP = bcadd($flValorTMP, $arTMP['valor'], 2);
            }
            if ($flValorTMP != $flValorRisco) {
                $obErro->setDescricao('A soma do valor das providências deve ser igual ao valor do risco fiscal.');
            }
        }

        if (!$obErro->ocorreu()) {
            $this->obModel->stDescricao = $arParam['stDescricaoRisco'];
            $this->obModel->flValor = $arParam['flValor'];
            $this->obModel->stExercicio = $arParam['stExercicio'];
            $this->obModel->inCodEntidade = $arParam['inCodEntidade'];
            $this->obModel->inCodRisco = $arParam['inCodRisco'];
            $this->obModel->inCodIdentificador = $arParam['inCodIdentificador'];

            if (!$obErro->ocorreu()) {
                $this->obModel->listProvidencias($rsProvidencias);
                while (!$rsProvidencias->EOF()) {
                    $this->obModel->inCodProvidencia = $rsProvidencias->getCampo('cod_providencia');
                    $obErro = $this->obModel->excluirProvidencias(false, $boTransacao);
                    $rsProvidencias->proximo();
                }
            }

            if (!$obErro->ocorreu()) {
                $obErro = $this->obModel->incluirRiscosFiscais(false, $boTransacao);

                //Inclui as Providencias que nao existem na base
                $arProvidencia = Sessao::read('arProvidencia');
                foreach ($arProvidencia as $arAux) {
                    $this->obModel->inCodProvidencia   = $arAux['cod_providencia'];
                    $this->obModel->stDescricao        = $arAux['descricao'];
                    $this->obModel->flValorProvidencia = $arAux['valor'];

                    $obErro = $this->obModel->incluirProvidencias(false, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                $stFiltro .= "&stExercicio=".$arParam['stExercicio'];
                $stFiltro .= "&inCodEntidade=".$arParam['inCodEntidade'];
                $stFiltro .= "&stAcao=alterar";
                $arParam['stAcao'] = 'alterar';
                SistemaLegado::alertaAviso('LSManterRiscosFiscais.php'.'?'.Sessao::getId().$stFiltro, $arParam['inCodRisco'],$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_alterar', 'erro');
        }
    }

    /**
     * Metodo excluirDemonstrativo, altera os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirDemonstrativo($arParam)
    {
        $obErro = new Erro();

        if (!$obErro->ocorreu()) {
            $this->obModel->stExercicio = $arParam['stExercicio'];
            $this->obModel->inCodEntidade = $arParam['inCodEntidade'];
            $this->obModel->inCodRisco = $arParam['inCodRisco'];

            if (!$obErro->ocorreu()) {
                $this->obModel->listProvidencias($rsProvidencias);
                while (!$rsProvidencias->EOF()) {
                    $this->obModel->inCodProvidencia = $rsProvidencias->getCampo('cod_providencia');
                    $obErro = $this->obModel->excluirProvidencias(false, $boTransacao);
                    $rsProvidencias->proximo();
                }
            }

            if (!$obErro->ocorreu()) {
                $obErro = $this->obModel->excluirRiscosFiscais(false, $boTransacao);
            }

            if (!$obErro->ocorreu()) {
                $stFiltro .= "&stExercicio=".$arParam['stExercicio'];
                $stFiltro .= "&inCodEntidade=".$arParam['inCodEntidade'];
                $stFiltro .= "&stAcao=excluir";
                $arParam['stAcao'] = 'excluir';
                SistemaLegado::alertaAviso('LSManterRiscosFiscais.php'.'?'.Sessao::getId().$stFiltro, $arParam['inCodRisco'],$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_excluir', 'erro');
        }
    }

    /**
     * Metodo buldListaReceitaAnexo3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return string
     */
    public function buildListaReceitaAnexo3(array $arDados)
    {
        $rsReceita = new RecordSet;
        $rsReceita->preenche($arDados);

        $table = new Table;
        $table->setRecordset  ($rsReceita);
        $table->setSummary    ('Receitas Vinculadas');
        //$table->setConditional(true, '#efefef');

        $table->Head->addCabecalho('Tipo',20);
        $table->Head->addCabecalho('Exercicio', 10);
        $table->Head->addCabecalho('Receita'  , 70);

        $table->Body->addCampo('nom_tipo'                   ,'E');
        $table->Body->addCampo('exercicio'                  ,'C');
        $table->Body->addCampo('[cod_receita] - [descricao]','E');

        $table->Body->addAcao('excluir', "ajaxJavaScript('OCManterAnexo3RCL.php?&cod_receita=%s&cod_tipo=%s','excluirReceitaAnexo3');", array('cod_receita','cod_tipo'));

        $table->montaHTML(true);

        $stJs.= "\n jq('#spnLista').html('".$table->getHtml()."');";

        return $stJs;
    }

    /**
     * Metodo incluirReceitaAnexo3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return string
     */
    public function incluirReceitaAnexo3(array $arParam)
    {
        $obErro = new Erro();
        $arReceitas = (array) Sessao::read('receitas');

        if ($arParam['stExercicio'] == '') {
            $obErro->setDescricao('Informe o Exercício');
        } elseif ($arParam['inCodReceita'] == '') {
            $obErro->setDescricao('Informe a Receita');
        } elseif ($arParam['inCodTipo'] == '') {
            $obErro->setDescricao('Informe a Tipo da Receita');
        }

        if (!$obErro->ocorreu()) {
            foreach ($arReceitas as $arReceita) {
                if (($arReceita['cod_receita'] == $arParam['inCodReceita']) AND ($arReceita['cod_tipo'] == $arParam['inCodTipo'])) {
                    $obErro->setDescricao('A Receita já está na lista');
                }
            }
        }

        if (!$obErro->ocorreu()) {
            include CAM_GF_ORC_NEGOCIO . 'ROrcamentoReceita.class.php';
            $obROrcamentoReceita = new ROrcamentoReceita();
            $obROrcamentoReceita->setExercicio($arParam['stExercicio']);
            $obROrcamentoReceita->setCodReceita($arParam['inCodReceita']);
            $obROrcamentoReceita->listar($rsReceita);

            $this->obModel->inCodTipoReceita = $arParam['inCodTipo'];
            $this->obModel->listTipoReceitasAnexo3($rsTipo);

            $arReceitas[] = array(
                'exercicio'   => $arParam['stExercicio'],
                'cod_receita' => $arParam['inCodReceita'],
                'cod_tipo'    => $rsTipo->getCampo('cod_tipo'),
                'nom_tipo'    => $rsTipo->getCampo('descricao'),
                'descricao'   => $rsReceita->getCampo('descricao'),
                'new'         => true
            );
            $arReceitas = Sessao::write('receitas',$arReceitas);

            $stJs.= "jq('input#inCodReceita').val('');";
            $stJs.= "jq('#stNomReceita').html('&nbsp;');";
            $stJs.= "jq('#inCodTipo').selectOptions('',true);";
            $stJs.= $this->buildListaReceitaAnexo3($arReceitas);
        } else {
            $stJs.= "alertaAviso('" . $obErro->getDescricao() . ".','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;
    }

    /**
     * Metodo excluirReceitaAnexo3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirReceitaAnexo3(array $arParam)
    {
        $arReceitas    = (array) Sessao::read('receitas');
        $arReceitasDel = (array) Sessao::read('receitas_del');
        $arReceitasNew = array();
        if ($arParam['cod_receita']) {
            foreach ($arReceitas as $arReceita) {
                if (($arReceita['cod_receita'] == $arParam['cod_receita']) AND ($arReceita['cod_tipo'] == $arParam['cod_tipo'])) {
                    $arReceitasDel[] = $arReceita;
                } else {
                    $arReceitasNew[] = $arReceita;
                }
            }
        } else {
            $arReceitasNew = array();
        }
        Sessao::write('receitas'    ,$arReceitasNew);
        Sessao::write('receitas_del',$arReceitasDel);

        $stJs.= $this->buildListaReceitaAnexo3($arReceitasNew);

        echo $stJs;
    }

    /**
     * Metodo listReceitaAnexo3, retorna as receitas vinculadas
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function carregaReceitasAnexo3(array $arParam)
    {
        //recupera as receitas
        $obErro = $this->obModel->listReceitasAnexo3($rsReceitas);
        $arReceitas = array();
        while (!$rsReceitas->eof()) {
            $arReceitas[] = array (
                'exercicio'   => $rsReceitas->getCampo('exercicio'),
                'cod_receita' => $rsReceitas->getCampo('cod_receita'),
                'cod_tipo'    => $rsReceitas->getCampo('cod_tipo'),
                'nom_tipo'    => $rsReceitas->getCampo('nom_tipo'),
                'descricao'   => $rsReceitas->getCampo('descricao'),
                'new'         => false,
            );

            $rsReceitas->proximo();
        }

        Sessao::write('receitas',$arReceitas);

        //recupera a config do irrf
        $this->obModel->obTAdministracaoConfiguracao->setDado('exercicio' , Sessao::getExercicio());
        $this->obModel->obTAdministracaoConfiguracao->setDado('cod_modulo', 36);
        $this->obModel->obTAdministracaoConfiguracao->setDado('parametro', 'deduzir_irrf_anexo_3');
        $this->obModel->obTAdministracaoConfiguracao->recuperaPorChave($rsConfig);

        if ($rsConfig->getCampo('valor') == 'true') {
            $stJs .= "jq('select#boIRRF').selectOptions('1');";
        } elseif ($rsConfig->getCampo('valor') == 'false') {
            $stJs .= "jq('select#boIRRF').selectOptions('0');";
        }

        $stJs .= $this->buildListaReceitaAnexo3($arReceitas);

        echo $stJs;
    }

    /**
     * Metodo incluirAnexo3, faz a inclusao dos dados na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirAnexo3(array $arParam)
    {

        $obErro = new Erro();
        foreach ((array) Sessao::read('receitas_del') as $receita) {
            $this->obModel->inCodReceita      = $receita['cod_receita'];
            $this->obModel->stExercicio       = $receita['exercicio'];
            $this->obModel->inCodTipoReceita  = $receita['cod_tipo'];
            $obErro = $this->obModel->excluirReceitaAnexo3($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            foreach ((array) Sessao::read('receitas') as $receita) {
                if ($receita['new']) {
                    $this->obModel->inCodReceita = $receita['cod_receita'];
                    $this->obModel->stExercicio  = $receita['exercicio'];
                    $this->obModel->inCodTipoReceita  = $receita['cod_tipo'];
                    $obErro = $this->obModel->incluirReceitaAnexo3($boTransacao);
                }
            }
        }
        if (!$obErro->ocorreu()) {
            $this->obModel->boIRRF = $arParam['boIRRF'];
            $obErro = $this->obModel->alterarConfiguracaoReceitaAnexo3($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FMManterAnexo3RCL.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Configuração concluída com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
        }
    }

}
