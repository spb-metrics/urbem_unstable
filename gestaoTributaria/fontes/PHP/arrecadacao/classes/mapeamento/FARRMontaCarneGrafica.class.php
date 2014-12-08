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
  * Função para montar cada linha do arquivo de carne para GRÁFICA

  * Data de Criação: 08/11/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Diego Bueno Coelho
  * @package URBEM
  * @subpackage Mapeamento

  * $Id: FARRMontaCarneGrafica.class.php 59612 2014-09-02 12:00:51Z gelson $

  * Casos de uso: uc-05.03.11
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 08/11/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento
*/
class FARRMontaCarneGrafica extends Persistente
{
    public $stTipoEmissao;

    /**
        * Método Construtor
        * @access Private
    */
    public function FARRMontaCarneGrafica()
    {
        parent::Persistente();
        //$this->setTabela('CalculoTributario');
        $this->stTipoEmissao            = null;
    }

    public function montaLinhaCarneGrafica($stTipoCodigoBarra, $rsCabecalho, $rsParcelas)
    {
        /* CONFIGURACAO DA LINHA DO CABECALHO */
        $arConfCabecalho = array (
            "0"     => "sigla_uf-2",
            "1"     => "nom_municipio-35",
            "2"     => "tipo_logradouro-15",
            "3"     => "logradouro-60",
            "4"     => "numero-6",
            "5"     => "complemento-160",
            "6"     => "bairro-30",
            "7"     => "cep-10",
            "8"     => "fone-10",
            "9"     => "email-160",
            "10"    => "cnpj-20"
        );

        $arConfCabecalhoCompensacao = array (
            "0"     => "local_pagamento-80",
            "1"     => "carteira-5",
            "2"     => "especie_doc-20",
            "3"     => "aceite-1",
            "4"     => "especie-20",
            "5"     => "quantidade-20",
            "6"     => "agencia-10",
            "7"     => "codigo_cedente-20"
        );

        /* CONFIGURACAO DA LINHA DO CARNE CARNE */
        if ($this->stTipoEmissao == "II" || !$this->stTipoEmissao) {

            $arConfLinha = array (
                "1" 	=> "numcgm-7",
                "2" 	=> "nom_cgm-200",
                
                // ENDEREÇO DE CORRESPONDENCIA
                "3" 	=> "c_nom_tipo_logradouro-15",
                "4" 	=> "c_cod_logradouro-7",
                "5" 	=> "c_nom_logradouro-60",
                "6" 	=> "c_numero-6",
                "7" 	=> "c_nom_bairro-30",
                "8" 	=> "c_nom_municipio-35",
                "9"	=> "c_sigla_uf-2",
                "10" 	=> "c_cep-8",
                "11" 	=> "c_complemento-160",
                "12" 	=> "c_caixa_postal-160",
                
                // ENDEREÇO DO IMOVEL
                "13" 	=> "nom_tipo_logradouro-15",
                "14" 	=> "cod_logradouro-7",
                "15" 	=> "nom_logradouro-60",
                "16" 	=> "numero-6",
                "17" 	=> "nom_bairro-30",
                "18" 	=> "nom_municipio-35",
                "19"	=> "sigla_uf-2",
                "20" 	=> "cep-8",
                "21" 	=> "complemento-160",
                
                //DADOS DO IMOVEL
                "22" 	=> "inscricao-7",
                "23" 	=> "area_lote-17",
                "24" 	=> "area_construida-17",
                "25" 	=> "codigo_composto-100",
                "26" 	=> "nom_localizacao-80",
                
                //DIVIDA
                "27" 	=> "cod_grupo-7",
                "28" 	=> "nom_grupo-80",
                "29"    => "exercicio-4",
                
                //VALORES DOS CREDITOS
                "30" 	=> "cod_credito_1-7",
                "31" 	=> "descricao_1-80",
                "32" 	=> "valor_1-17",
                "33" 	=> "cod_credito_2-7",
                "34" 	=> "descricao_2-80",
                "35" 	=> "valor_2-17",
                "36" 	=> "cod_credito_3-7",
                "37" 	=> "descricao_3-80",
                "38" 	=> "valor_3-17",
                "39" 	=> "cod_credito_4-7",
                "40" 	=> "descricao_4-80",
                "41" 	=> "valor_4-17",
                "42" 	=> "cod_credito_5-7",
                "43" 	=> "descricao_5-80",
                "44" 	=> "valor_5-17",
                "45" 	=> "cod_credito_6-7",
                "46" 	=> "descricao_6-80",
                "47" 	=> "valor_6-17",
                "48" 	=> "cod_credito_7-7",
                "49" 	=> "descricao_7-80",
                "50" 	=> "valor_7-17",
                "51" 	=> "soma_creditos-17",
                
                //PARCELAS UNICAS
                "52" 	=> "valor_unica_1-17",
                "53" 	=> "vencimento_unica_1-10",
                "54" 	=> "desconto_unica_1-17",
                "55" 	=> "nosso_numero_unica_1-17",
                "56" 	=> "codigo_barra_unica_1-60",
                "57" 	=> "linha_digitavel_unica_1-120",
                "58" 	=> "valor_unica_2-17",
                "59" 	=> "vencimento_unica_2-10",
                "60" 	=> "desconto_unica_2-17",
                "61" 	=> "nosso_numero_unica_2-17",
                "62" 	=> "codigo_barra_unica_2-60",
                "63" 	=> "linha_digitavel_unica_2-120",
                "64" 	=> "valor_unica_3-17",
                "65" 	=> "vencimento_unica_3-10",
                "66" 	=> "desconto_unica_3-17",
                "67" 	=> "nosso_numero_unica_3-17",
                "68" 	=> "codigo_barra_unica_3-60",
                "69" 	=> "linha_digitavel_unica_3-120",
                "70" 	=> "valor_unica_4-17",
                "71" 	=> "vencimento_unica_4-10",
                "72" 	=> "desconto_unica_4-17",
                "73" 	=> "nosso_numero_unica_4-17",
                "74" 	=> "codigo_barra_unica_4-60",
                "75" 	=> "linha_digitavel_unica_4-120",
                "76" 	=> "valor_unica_5-17",
                "77" 	=> "vencimento_unica_5-10",
                "78" 	=> "desconto_unica_5-17",
                "79" 	=> "nosso_numero_unica_5-17",
                "80" 	=> "codigo_barra_unica_5-60",
                "81" 	=> "linha_digitavel_unica_5-120",
                
                //PARCELAS NORMAIS
                "82" 	=> "valor_normal_1-17",
                "83" 	=> "vencimento_normal_1-10",
                "84" 	=> "nosso_numero_normal_1-17",
                "85" 	=> "codigo_barra_normal_1-60",
                "86" 	=> "linha_digitavel_normal_1-120",

                "87" 	=> "valor_normal_2-17",
                "88" 	=> "vencimento_normal_2-10",
                "89" 	=> "nosso_numero_normal_2-17",
                "90" 	=> "codigo_barra_normal_2-60",
                "91" 	=> "linha_digitavel_normal_2-120",

                "92" 	=> "valor_normal_3-17",
                "93" 	=> "vencimento_normal_3-10",
                "94" 	=> "nosso_numero_normal_3-17",
                "95" 	=> "codigo_barra_normal_3-60",
                "96" 	=> "linha_digitavel_normal_3-120",

                "97" 	=> "valor_normal_4-17",
                "98" 	=> "vencimento_normal_4-10",
                "99" 	=> "nosso_numero_normal_4-17",
                "100" 	=> "codigo_barra_normal_4-60",
                "101" 	=> "linha_digitavel_normal_4-120",

                "102" 	=> "valor_normal_5-17",
                "103" 	=> "vencimento_normal_5-10",
                "104" 	=> "nosso_numero_normal_5-120",
                "105" 	=> "codigo_barra_normal_5-60",
                "106" 	=> "linha_digitavel_normal_5-120",

                "107" 	=> "valor_normal_6-17",
                "108" 	=> "vencimento_normal_6-10",
                "109" 	=> "nosso_numero_normal_6-17",
                "110" 	=> "codigo_barra_normal_6-60",
                "111" 	=> "linha_digitavel_normal_6-120",

                "112" 	=> "valor_normal_7-17",
                "113" 	=> "vencimento_normal_7-10",
                "114" 	=> "nosso_numero_normal_7-17",
                "115" 	=> "codigo_barra_normal_7-60",
                "116" 	=> "linha_digitavel_normal_7-120",

                "117" 	=> "valor_normal_8-17",
                "118" 	=> "vencimento_normal_8-10",
                "119" 	=> "nosso_numero_normal_8-17",
                "120" 	=> "codigo_barra_normal_8-60",
                "121" 	=> "linha_digitavel_normal_8-120",

                "122" 	=> "valor_normal_9-17",
                "123" 	=> "vencimento_normal_9-10",
                "124" 	=> "nosso_numero_normal_9-17",
                "125" 	=> "codigo_barra_normal_9-60",
                "126" 	=> "linha_digitavel_normal_9-120",

                "127" 	=> "valor_normal_10-17",
                "128" 	=> "vencimento_normal_10-10",
                "129" 	=> "nosso_numero_normal_10-17",
                "130" 	=> "codigo_barra_normal_10-60",
                "131" 	=> "linha_digitavel_normal_10-120",

                "132" 	=> "valor_normal_11-17",
                "133" 	=> "vencimento_normal_11-10",
                "134" 	=> "nosso_numero_normal_11-17",
                "135" 	=> "codigo_barra_normal_11-60",
                "136" 	=> "linha_digitavel_normal_11-120",

                "137" 	=> "valor_normal_12-17",
                "138" 	=> "vencimento_normal_12-10",
                "139" 	=> "nosso_numero_normal_12-17",
                "140" 	=> "codigo_barra_normal_12-60",
                "141" 	=> "linha_digitavel_normal_12-120",
                
                # VALORES VENAIS
                "142"   => "valor_venal_territorial-17",
                "143"   => "valor_venal_predial-17",
                "144"   => "valor_venal_total-17",
                
                # VALORES VUP
                "145"   => "valor_m2_territorial-17",
                "146"   => "valor_m2_predial-17",
                
                # NOME LOCALIZACAO PRIMEIRO NIVEL
                "147"   => "localizacao_primeiro_nivel-80",
                
                # VALOR IMPOSTO
                "148"   => "valor_imposto-17",

                "149"   => "area_limpeza-17",
                "150"   => "aliquota_limpeza-8",
                "151"   => "aliquota_imposto-8",
                
                # ATRIBUTOS DINAMICOS (MAXIMO 15)
                "152" 	=> "atributo_1-50",
                "153" 	=> "atributo_2-50",
                "154" 	=> "atributo_3-50",
                "155" 	=> "atributo_4-50",
                "156" 	=> "atributo_5-50",
                "157" 	=> "atributo_6-50",
                "158" 	=> "atributo_7-50",
                "159" 	=> "atributo_8-50",
                "160" 	=> "atributo_9-50",
                "161" 	=> "atributo_10-50",
                "162" 	=> "atributo_11-50",
                "163" 	=> "atributo_12-50",
                "164" 	=> "atributo_13-50",
                "165" 	=> "atributo_14-50",
                "166" 	=> "atributo_15-50",
                "167"   => "valor_m2_predial_descoberto-17",
                "168"   => "valor_venal_predial_descoberto-17",
                "169"   => "area_construida_total-17",
                "170"   => "area_descoberta-17",
                "171"   => "valor_venal_predial_coberto-17"
            );
        } else {
            # LAYOUT PARA CARNE DA INSCR. ECONÔMICA
            $arConfLinha = array (

                # DETALHES DO CARNE
                "1"   => "numcgm-7",
                "2"   => "nom_cgm-200",

                # ENDEREÇO DA EMPRESA
                "3"   => "c_nom_tipo_logradouro-15",
                "4"   => "c_cod_logradouro-7",
                "5"   => "c_nom_logradouro-60",
                "6"   => "c_numero-6",
                "7"   => "c_nom_bairro-30",
                "8"   => "c_nom_municipio-35",
                "9"   => "c_sigla_uf-2",
                "10"  => "c_cep-8",
                "11"  => "c_complemento-160",
                "12"  => "c_caixa_postal-160",

                # DADOS DA EMPRESA
                "13"  => "inscricao_economica-8",
                "14"  => "data_abertura-10",
                "15"  => "numcgm_responsavel-7",
                "16"  => "nome_responsavel-200",
                "17"  => "cod_natureza-5",
                "18"  => "natureza_juridica-200",
                "19"  => "cod_categoria-2",
                "20"  => "categoria-40",
                "21"  => "cod_atividade_principal-15",
                "22"  => "descricao_atividade_principal-240",
                "23"  => "data_inicio-10",
                "24"  => "cnpj-20",
                "25"  => "nom_fantasia-150",
                "26"  => "inscricao_municipal_economica-15",

                # RELAÇÃO SÓCIOS
                "27"  => "numcgm_socio_1-7",
                "28"  => "nome_socio_1-200",
                "29"  => "quota_socio_1-6",

                "30"  => "numcgm_socio_2-7",
                "31"  => "nome_socio_2-200",
                "32"  => "quota_socio_2-6",

                "33"  => "numcgm_socio_3-7",
                "34"  => "nome_socio_3-200",
                "35"  => "quota_socio_3-6",

                "36"  => "numcgm_socio_4-7",
                "37"  => "nome_socio_4-200",
                "38"  => "quota_socio_4-6",

                "39"  => "numcgm_socio_5-7",
                "40"  => "nome_socio_5-200",
                "41"  => "quota_socio_5-6",

                # DÍVIDA
                "42"  => "cod_grupo-7",
                "43"  => "nom_grupo-80",
                "44"  => "exercicio-4",

                # VALORES DOS CRÉDITOS
                "45"  => "cod_credito_1-7",
                "46"  => "descricao_1-80",
                "47"  => "valor_1-17",

                "48"  => "cod_credito_2-7",
                "49"  => "descricao_2-80",
                "50"  => "valor_2-17",

                "51"  => "cod_credito_3-7",
                "52"  => "descricao_3-80",
                "53"  => "valor_3-17",

                "54"  => "cod_credito_4-7",
                "55"  => "descricao_4-80",
                "56"  => "valor_4-17",

                "57"  => "cod_credito_5-7",
                "58"  => "descricao_5-80",
                "59"  => "valor_5-17",

                "60"  => "cod_credito_6-7",
                "61"  => "descricao_6-80",
                "62"  => "valor_6-17",

                "63"  => "cod_credito_7-7",
                "64"  => "descricao_7-80",
                "65"  => "valor_7-17",

                "66"  => "soma_creditos-17",

                # PARCELAS ÚNICAS
                "67"  => "valor_unica_1-17",
                "68"  => "vencimento_unica_1-10",
                "69"  => "desconto_unica_1-17",
                "70"  => "nosso_numero_unica_1-17",
                "71"  => "codigo_barra_unica_1-60",
                "72"  => "linha_digitavel_unica_1-120",

                "73"  => "valor_unica_2-17",
                "74"  => "vencimento_unica_2-10",
                "75"  => "desconto_unica_2-17",
                "76"  => "nosso_numero_unica_2-17",
                "77"  => "codigo_barra_unica_2-60",
                "78"  => "linha_digitavel_unica_2-120",

                "79"  => "valor_unica_3-17",
                "80"  => "vencimento_unica_3-10",
                "81"  => "desconto_unica_3-17",
                "82"  => "nosso_numero_unica_3-17",
                "83"  => "codigo_barra_unica_3-60",
                "84"  => "linha_digitavel_unica_3-120",

                "85"  => "valor_unica_4-17",
                "86"  => "vencimento_unica_4-10",
                "87"  => "desconto_unica_4-17",
                "88"  => "nosso_numero_unica_4-17",
                "89"  => "codigo_barra_unica_4-60",
                "90"  => "linha_digitavel_unica_4-120",

                "91"  => "valor_unica_5-17",
                "92"  => "vencimento_unica_5-10",
                "93"  => "desconto_unica_5-17",
                "94"  => "nosso_numero_unica_5-17",
                "95"  => "codigo_barra_unica_5-60",
                "96"  => "linha_digitavel_unica_5-120",

                # PARCELAS NORMAIS
                "97"  => "valor_normal_1-17",
                "98"  => "vencimento_normal_1-10",
                "99"  => "nosso_numero_normal_1-17",
                "100"  => "codigo_barra_normal_1-60",
                "101"  => "linha_digitavel_normal_1-120",

                "102" => "valor_normal_2-17",
                "103" => "vencimento_normal_2-10",
                "104" => "nosso_numero_normal_2-17",
                "105" => "codigo_barra_normal_2-60",
                "106" => "linha_digitavel_normal_2-120",

                "107" => "valor_normal_3-17",
                "108" => "vencimento_normal_3-10",
                "109" => "nosso_numero_normal_3-17",
                "110" => "codigo_barra_normal_3-60",
                "111" => "linha_digitavel_normal_3-120",

                "112" => "valor_normal_4-17",
                "113" => "vencimento_normal_4-10",
                "114" => "nosso_numero_normal_4-17",
                "115" => "codigo_barra_normal_4-60",
                "116" => "linha_digitavel_normal_4-120",

                "117" => "valor_normal_5-17",
                "118" => "vencimento_normal_5-10",
                "119" => "nosso_numero_normal_5-120",
                "120" => "codigo_barra_normal_5-60",
                "121" => "linha_digitavel_normal_5-120",

                "122" => "valor_normal_6-17",
                "123" => "vencimento_normal_6-10",
                "124" => "nosso_numero_normal_6-17",
                "125" => "codigo_barra_normal_6-60",
                "126" => "linha_digitavel_normal_6-120",

                "127" => "valor_normal_7-17",
                "128" => "vencimento_normal_7-10",
                "129" => "nosso_numero_normal_7-17",
                "130" => "codigo_barra_normal_7-60",
                "131" => "linha_digitavel_normal_7-120",

                "132" => "valor_normal_8-17",
                "133" => "vencimento_normal_8-10",
                "134" => "nosso_numero_normal_8-17",
                "135" => "codigo_barra_normal_8-60",
                "136" => "linha_digitavel_normal_8-120",

                "137" => "valor_normal_9-17",
                "138" => "vencimento_normal_9-10",
                "139" => "nosso_numero_normal_9-17",
                "140" => "codigo_barra_normal_9-60",
                "141" => "linha_digitavel_normal_9-120",

                "142" => "valor_normal_10-17",
                "143" => "vencimento_normal_10-10",
                "144" => "nosso_numero_normal_10-17",
                "145" => "codigo_barra_normal_10-60",
                "146" => "linha_digitavel_normal_10-120",

                "147" => "valor_normal_11-17",
                "148" => "vencimento_normal_11-10",
                "149" => "nosso_numero_normal_11-17",
                "150" => "codigo_barra_normal_11-60",
                "151" => "linha_digitavel_normal_11-120",

                "152" => "valor_normal_12-17",
                "153" => "vencimento_normal_12-10",
                "154" => "nosso_numero_normal_12-17",
                "155" => "codigo_barra_normal_12-60",
                "156" => "linha_digitavel_normal_12-120",

                "157"  => "quadra-80",
                "158"  => "lote-80",
                "159"  => "nom_localizacao-80",
                "160"  => "localizacao_primeiro_nivel-80",

                # ATRIBUTOS DINÂMICOS (MÁXIMO 15)
                "161" => "atributo_1-50",
                "163" => "atributo_2-50",
                "163" => "atributo_3-50",
                "164" => "atributo_4-50",
                "165" => "atributo_5-50",
                "166" => "atributo_6-50",
                "167" => "atributo_7-50",
                "168" => "atributo_8-50",
                "169" => "atributo_9-50",
                "170" => "atributo_10-50",
                "171" => "atributo_11-50",
                "172" => "atributo_12-50",
                "173" => "atributo_13-50",
                "174" => "atributo_14-50",
                "175" => "atributo_15-50"
            );

        }

        $contConf = count ($arConfLinha);

        $arRetorno  = array();

        $arLinhaCabecalho = array();
        $arRetorno[] = $stTipoCodigoBarra;
        $arRetorno[] = $rsCabecalho->getCampo('prefeitura');
        $arRetorno[] = $rsCabecalho->getCampo('cod_febraban');

        $stRetorno = null;
        $cont = 0;
        
        # Cabeçalho do arquivo exportado.
        while ($cont < count($arConfCabecalho)) {
        $artmp = explode ('-',$arConfCabecalho[$cont]);
        
        $colunaAtualConf = $artmp[0];
        $tamColunaAtual  = $artmp[1];

        $valorRecordSet = $rsCabecalho->getCampo($colunaAtualConf);

        $contTam = 0;

        $stRetorno .= $valorRecordSet.'§';
        $cont++;
        }

        $arRetorno[] = $stRetorno;

        $stRetorno = null;
        $cont = 0;

        while ($cont < count($arConfCabecalhoCompensacao)) {

            $artmp = explode ('-',$arConfCabecalhoCompensacao[$cont]);
            $colunaAtualConf = $artmp[0];
            $tamColunaAtual  = $artmp[1];

            $valorRecordSet = $rsCabecalho->getCampo( $colunaAtualConf );

            # Separador por colunas, solicitado para exportar como .csv
            $stRetorno .= $valorRecordSet.'§';
            $cont++;
        }

        $arRetorno[] = date("d/m/Y").'§'.$stRetorno;
        $arRetorno[] = "$$";

        # Quando for Inscr. Econ. monta a linha com esse cabeçalho.
        if ($this->stTipoEmissao == "IE") {
            $arRetorno[] = "numcgm§nom_cgm§c_nom_tipo_logradouro§c_cod_logradouro§c_nom_logradouro§c_numero§c_nom_bairro§c_nom_municipio§c_sigla_uf§c_cep§c_complemento§c_caixa_postal§inscricao_economica§data_abertura§numcgm_responsavel§nome_responsavel§cod_natureza§natureza_juridica§cod_categoria§categoria§cod_atividade_principal§descricao_atividade_principal§data_inicio§cnpj§nome_fantasia§inscricao_imobiliaria§numcgm_socio_1§nome_socio_1§quota_socio_1§numcgm_socio_2§nome_socio_2§quota_socio_2§numcgm_socio_3§nome_socio_3§quota_socio_3§numcgm_4§nome_socio_4§quota_socio_4§numcgm_5§nome_socio_5§quota_socio_5§cod_grupo§nom_grupo§Exercício§cod_credito_1§descricao_1§valor_1§cod_credito_2§descricao_2§valor_2§cod_credito_3§descricao_3§valor_3§cod_credito_4§descricao_4§valor_4§cod_credito_5§descricao_5§valor_5§cod_credito_6§descricao_6§valor_6§cod_credito_7§descricao_7§valor_7§soma_creditos§valor_unica_1§vencimento_unica_1§desconto_unica_1§nosso_numero_unica_1§codigo_barra_unica_1§linha_digitavel_unica_1§valor_unica_2§vencimento_unica_2§desconto_unica_2§nosso_numero_unica_2§codigo_barra_unica_2§linha_digitavel_unica_2§valor_unica_3§vencimento_unica_3§desconto_unica_3§nosso_numero_unica_3§codigo_barra_unica_3§linha_digitavel_unica_3§valor_unica_4§vencimento_unica_4§desconto_unica_4§nosso_numero_unica_4§codigo_barra_unica_4§linha_digitavel_unica_4§valor_unica_5§vencimento_unica_5§desconto_unica_5§nosso_numero_unica_5§codigo_barra_unica_5§linha_digitavel_unica_5§valor_normal_1§vencimento_normal_1§nosso_numero_normal_1§codigo_barra_normal_1§linha_digitavel_normal_1§valor_normal_2§vencimento_normal_2§nosso_numero_normal_2§codigo_barra_normal_2§linha_digitavel_normal_2§valor_normal_3§vencimento_normal_3§nosso_numero_normal_3§codigo_barra_normal_3§linha_digitavel_normal_3§valor_normal_4§vencimento_normal_4§nosso_numero_normal_4§codigo_barra_normal_4§linha_digitavel_normal_4§valor_normal_5§vencimento_normal_5§nosso_numero_normal_5§codigo_barra_normal_5§linha_digitavel_normal_5§valor_normal_6§vencimento_normal_6§nosso_numero_normal_6§codigo_barra_normal_6§linha_digitavel_normal_6§valor_normal_7§vencimento_normal_7§nosso_numero_normal_7§codigo_barra_normal_7§linha_digitavel_normal_7§valor_normal_8§vencimento_normal_8§nosso_numero_normal_8§codigo_barra_normal_8§linha_digitavel_normal_8§valor_normal_9§vencimento_normal_9§nosso_numero_normal_9§codigo_barra_normal_9§linha_digitavel_normal_9§valor_normal_10§vencimento_normal_10§nosso_numero_normal_10§codigo_barra_normal_10§linha_digitavel_normal_10§valor_normal_11§vencimento_normal_11§nosso_numero_normal_11§codigo_barra_normal_11§linha_digitavel_normal_11§valor_normal_12§vencimento_normal_12§nosso_numero_normal_12§codigo_barra_normal_12§linha_digitavel_normal_12§quadra§lote§distrito§regiao§atributo_1§atributo_2§atributo_3§atributo_4§atributo_5§atributo_6§atributo_7§atributo_8§atributo_9§atributo_10§atributo_11§atributo_12§atributo_13§atributo_14§atributo_15";
        } elseif ($this->stTipoEmissao == "II") {
            $arRetorno[] = "numcgm§nom_cgm§c_nom_tipo_logradouro§c_cod_logradouro§c_nom_logradouro§c_numero§c_nom_bairro§c_nom_municipio§c_sigla_uf§c_cep§c_complemento§c_caixa_postal§nom_tipo_logradouro§cod_logradouro§nom_logradouro§numero§bairro§nom_municipio§sigla_uf§cep§complemento§inscricao_municipal§area_lote§area_construida§codigo_composto§nom_localizacao§cod_grupo§nom_grupo§Exercício§cod_credito_1§descricao_1§valor_1§cod_credito_2§descricao_2§valor_2§cod_credito_3§descricao_3§valor_3§cod_credito_4§descricao_4§valor_4§cod_credito_5§descricao_5§valor_5§cod_credito_6§descricao_6§valor_6§cod_credito_7§descricao_7§valor_7§soma_creditos§valor_unica_1§vencimento_unica_1§desconto_unica_1§nosso_numero_unica_1§codigo_barra_unica_1§linha_digitavel_unica_1§valor_unica_2§vencimento_unica_2§desconto_unica_2§nosso_numero_unica_2§codigo_barra_unica_2§linha_digitavel_unica_2§valor_unica_3§vencimento_unica_3§desconto_unica_3§nosso_numero_unica_3§codigo_barra_unica_3§linha_digitavel_unica_3§valor_unica_4§vencimento_unica_4§desconto_unica_4§nosso_numero_unica_4§codigo_barra_unica_4§linha_digitavel_unica_4§valor_unica_5§vencimento_unica_5§desconto_unica_5§nosso_numero_unica_5§codigo_barra_unica_5§linha_digitavel_unica_5§valor_normal_1§vencimento_normal_1§nosso_numero_normal_1§codigo_barra_normal_1§linha_digitavel_normal_1§valor_normal_2§vencimento_normal_2§nosso_numero_normal_2§codigo_barra_normal_2§linha_digitavel_normal_2§valor_normal_3§vencimento_normal_3§nosso_numero_normal_3§codigo_barra_normal_3§linha_digitavel_normal_3§valor_normal_4§vencimento_normal_4§nosso_numero_normal_4§codigo_barra_normal_4§linha_digitavel_normal_4§valor_normal_5§vencimento_normal_5§nosso_numero_normal_5§codigo_barra_normal_5§linha_digitavel_normal_5§valor_normal_6§vencimento_normal_6§nosso_numero_normal_6§codigo_barra_normal_6§linha_digitavel_normal_6§valor_normal_7§vencimento_normal_7§nosso_numero_normal_7§codigo_barra_normal_7§linha_digitavel_normal_7§valor_normal_8§vencimento_normal_8§nosso_numero_normal_8§codigo_barra_normal_8§linha_digitavel_normal_8§valor_normal_9§vencimento_normal_9§nosso_numero_normal_9§codigo_barra_normal_9§linha_digitavel_normal_9§valor_normal_10§vencimento_normal_10§nosso_numero_normal_10§codigo_barra_normal_10§linha_digitavel_normal_10§valor_normal_11§vencimento_normal_11§nosso_numero_normal_11§codigo_barra_normal_11§linha_digitavel_normal_11§valor_normal_12§vencimento_normal_12§nosso_numero_normal_12§codigo_barra_normal_12§linha_digitavel_normal_12§valor_venal_territorial§valor_venal_predial§valor_venal_total§valor_m2_territorial§valor_m2_predial§localizacao_primeiro_nivel§valor_imposto§area_limpeza§aliquota_limpeza§aliquota_imposto§atributo_1§atributo_2§atributo_3§atributo_4§atributo_5§atributo_6§atributo_7§atributo_8§atributo_9§atributo_10§atributo_11§atributo_12§atributo_13§atributo_14§atributo_15§valor_m2_predial_descoberto§valor_venal_predial_descoberto§area_construida_total§area_descoberta§valor_venal_predial_coberto";
        }

        # Percorre o RecordSet para preencher o arquivo com a consulta da PL.
        $rsParcelas->setPrimeiroElemento();
        while (!$rsParcelas->eof()) {

            $stRetorno = null;
            $cont = 1;
            while ($cont <= $contConf) {

                $artmp = explode ('-',$arConfLinha[$cont]);
                $colunaAtualConf = $artmp[0];
                $tamColunaAtual  = $artmp[1];

                if ( ( preg_match( '/codigo_barra_/i',$colunaAtualConf) ) || ( preg_match('/linha_digitavel_/i',$colunaAtualConf) ) ) {
                    $valorRecordSet = trim($rsParcelas->getCampo($colunaAtualConf));
                } else {
                    $valorRecordSet = $rsParcelas->getCampo($colunaAtualConf);
                }

                if (strlen($valorRecordSet) > $tamColunaAtual) {
                    $valorRecordSet  = mb_substr($valorRecordSet, 0, $tamColunaAtual,'UTF-8');
                }

                $valorRecordSet .= '§';

                # Separador por colunas, solicitado para exportar como .csv
                $stRetorno .= $valorRecordSet;

                $cont++;
            }

            $arRetorno[] = $stRetorno;
            $rsParcelas->proximo();
        }

        $rsRetorno = new RecordSet;
        $rsRetorno->preenche($arRetorno);

        return $rsRetorno;
    }
}

?>
