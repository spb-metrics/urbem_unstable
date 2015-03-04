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
 * Classe de controle - TCE/MG
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

class CTCEMGExportacao
{
    public $obModel;

    /**
     * Metodo construtor, seta o atributo obModel com o que vier na assinatura da funcao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $obModel Classe de Negocio
     *
     * @return void
     */
    public function __construct(&$obModel)
    {
        $this->obModel = $obModel;
    }

    /**
     * Metodo preencheEntidade, consulta as entidades e preenche no formulario
     *
     * @author      Analista            Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor       Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParams Array com parametros
     *
     * @return void
     */
    public function preencheEntidade($arParams)
    {
        $stJs .= "jq('#inCodEntidadeDisponivel').removeOption(/./);";
        $stJs .= "jq('#inCodEntidadeSelecionado').removeOption(/./);";
        $stJs .= "jq('#stTipoPeriodo').selectOptions('', true);";
        $stJs .= "jq('#inPeriodo').removeOption(/./);";
        $stJs .= "jq('#arArquivosSelecionado').removeOption(/./);";
        $stJs .= "jq('#arArquivosDisponivel').removeOption(/./);";

        if ($arParams['stTipoPoder'] != '') {

            $this->obModel->stPoder = $arParams['stTipoPoder'];
            $this->obModel->listEntidadePoder($rsEntidades);

            while (!$rsEntidades->eof()) {
                $stJs .= "jq('#inCodEntidadeDisponivel').addOption('" . $rsEntidades->getCampo('cod_entidade') . "', '" . $rsEntidades->getCampo('nom_cgm') . "');";

                $rsEntidades->proximo();
            }
        }

        echo $stJs;
    }

    /**
     * Metodo preenchePeriodo, monta os periodos
     *
     * @author      Analista            Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor       Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParams Array com parametros
     *
     * @return void
     */
    public function preenchePeriodo($arParams)
    {
        $stJs .= "jq('#inPeriodo').removeOption(/./);";
        $stJs .= "jq('#inPeriodo').addOption('','Selecione',true);";
        $stJs .= "jq('#arArquivosSelecionado').removeOption(/./);";
        $stJs .= "jq('#arArquivosDisponivel').removeOption(/./);";

        switch ($arParams['stTipoPeriodo']) {
        case 'mensal':
            $arMes = array(1 => 'Janeiro',
                           2 => 'Fevereiro',
                           3 => 'Março',
                           4 => 'Abril',
                           5 => 'Maio',
                           6 => 'Junho',
                           7 => 'Julho',
                           8 => 'Agosto',
                           9 => 'Setembro',
                          10 => 'Outubro',
                          11 => 'Novembro',
                          12 => 'Dezembro',
                          );

            foreach ($arMes as $inMes => $stMes) {
                $stJs .= "jq('#inPeriodo').addOption('" . $inMes . "','" . $stMes . "',false);";
            }

            break;
        case 'bimestral':
            for ($i=1; $i<=6; $i++) {
                $stJs .= "jq('#inPeriodo').addOption('" . $i . "','" . $i . "º Bimestre',false);";
            }

            break;
        }

        echo $stJs;
    }

    /**
     * Metodo preencheArquivo, monta os combos dos arquivos
     *
     * @author      Analista            Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor       Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParams Array com parametros
     *
     * @return void
     */
    public function preencheArquivo($arParams)
    {
        $stJs .= "jq('#arArquivosSelecionado').removeOption(/./);";
        $stJs .= "jq('#arArquivosDisponivel').removeOption(/./);";

        $arArquivo = array(
                        'ambos'       => array(
                                            'dispFinanceiras.txt',
                                            'inscRestosAPagar.txt',
                                            'servTerceiros.txt',
                                         ),
                        'legislativo' => array(
                                            'comparativoPL.txt',
                                            'despesaTotalPessoalPL.txt',
                                         ),
                        'executivo'   => array(
                                            'ativoPerm.txt',
                                            'comparativoPE.txt',
                                            'despesaCapital.txt',
                                            'despesaCorrente.txt',
                                            'despesaPrev.txt',
                                            'despesaTotalPessoalPE.txt',
                                            'despFuncaoSubfuncao.txt',
                                            'deducaoReceita.txt',
                                            'especifPrev.txt',
                                            'exclusaoDespesa.txt',
                                            'exclusaoReceita.txt',
                                            'execucaoVariacao.txt',
                                            'itemAtivoPassivo.txt',
                                            'metaArrecadacao.txt',
                                            'obsMetaArrecadacao.txt',
                                            'operacoesCreditoARO.txt',
                                            'passivoPerm.txt',
                                            'projecaoAtuarial.txt',
                                            'receitaPrev.txt',
                                            'receitaCorrente.txt',
                                            'receitaCapital.txt',
                                            'recursoAlienacaoAtivo.txt',
                                            'variacaoPatrimonial.txt',
                                            'demonstrativoOpCredito.txt',
                                         ),
                     );

        switch ($arParams['stTipoPoder']) {
        case 'ambos':
            if ($arParams['stTipoPeriodo'] == 'mensal') {
                foreach ($arArquivo['ambos'] as $arquivo) {
                    $stJs .= "jq('#arArquivosDisponivel').addOption('" . $arquivo . "','" . $arquivo . "');";
                }
            }

            break;
        case 'legislativo':
            if ($arParams['stTipoPeriodo'] == 'mensal') {
                foreach ($arArquivo['legislativo'] as $arquivo) {
                    $stJs .= "jq('#arArquivosDisponivel').addOption('" . $arquivo . "','" . $arquivo . "');";
                }
            }

            break;
        case 'executivo':
            if ($arParams['stTipoPeriodo'] == 'mensal') {
                foreach ($arArquivo['executivo'] as $arquivo) {
                    if ($arquivo != 'metaArrecadacao.txt') {
                        $stJs .= "jq('#arArquivosDisponivel').addOption('" . $arquivo . "','" . $arquivo . "');";
                    }
                }
            } elseif ($arParams['stTipoPeriodo'] == 'bimestral') {
                $stJs .= "jq('#arArquivosDisponivel').addOption('" . $arArquivo['executivo'][10] . "','" . $arArquivo['executivo'][10] . "');";
                $stJs .= "jq('#arArquivosDisponivel').addOption('" . $arArquivo['executivo'][11] . "','" . $arArquivo['executivo'][11] . "');";
            }

            break;
        }

        echo $stJs;

    }

    /**
     * Metodo montaArquivos, executa as acoes necessarias para gerar os arquivos
     *
     * @author      Analista            Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor       Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParams Array com parametros
     *
     * @return void
     */
    public function montaArquivos($arParams)
    {

        set_time_limit(0);

        include_once( CLA_EXPORTADOR );

        //SistemaLegado::BloqueiaFrames();

        $stAcao = $_REQUEST['stAcao'];

        $arFiltroRelatorio = Sessao::read('filtroRelatorio');

        $stTipoDocumento = "TCE_MG";
        $obExportador    = new Exportador();

        foreach ($arFiltroRelatorio["arArquivosSelecionado"] as $stArquivo) {
            $arArquivo = explode( '.',$stArquivo );
            $obExportador->addArquivo($arArquivo[0].'.'.$arArquivo[1]);
            $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);

            include( $arArquivo[0] . ".inc.php");

            $arRecordSet = null;
        }

        //if ($arFiltroRelatorio['stTipoExport'] == 'compactados') {
        //    $obExportador->setNomeArquivoZip('Balancete'.Sessao::getExercicio().$inMes.'.zip');
        //}

        $obExportador->show();
        //SistemaLegado::LiberaFrames();

    }

}
