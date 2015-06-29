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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 22/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 22/10/2007

  * @author Analista: Gelson Wolvowski Gonçalves
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTBAAdConv extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function TTBAAdConv() {}

    public function recuperaDadosConvenioAditivo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosConvenioAditivo().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosConvenioAditivo()
    {
        $stSql .= " SELECT 1 AS tipo_registro
                        , 0 AS unidade_gestora
                        , convenio.num_convenio
                        , convenio_aditivos.num_aditivo
                        -- reservado
                        , SUBSTR(TRIM(objeto.descricao), 1, 300) AS objeto_convenio
                        -- reservado
                        , TO_CHAR(convenio_aditivos.dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura_aditivo
                        --, TO_CHAR(convenio.dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura_convenio
                        , TO_CHAR(convenio.dt_vigencia, 'dd/mm/yyyy') AS dt_vigencia_convenio
                        , SUBSTR(TRIM(convenio_aditivos.fundamentacao), 1, 100) AS fundamentacao_legal_aditivo
                        -- reservado
                        , SUBSTR(TRIM(cgm_imprensa.nom_cgm), 1, 50) AS imprensa_oficial
                        , TO_CHAR(publicacao_convenio.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao_convenio
                        , convenio.valor
                        , 1 AS tipo_moeda
                        -- reservado
                        , TO_CHAR(convenio.dt_assinatura, 'yyyymm') AS competencia
                        , '' AS inicio_execucao
                        -- reservado
                        , 0 AS num_orgao
                        , 0 AS num_unidade
                        , 0 AS cod_programa
                        , 0 AS tipo_projeto_atividade
                        , 0 AS codigo_projeto_atividade
                        , 0 AS cod_despesa
                        , 0 AS fonte_recurso
                        , convenio_aditivos.exercicio AS ano
                        , 0 AS cod_funcao
                        , 0 AS cod_subfuncao
                        -- nro sequencial

                    FROM licitacao.convenio

                    INNER JOIN compras.objeto
                            ON convenio.cod_objeto = objeto.cod_objeto

                    INNER JOIN licitacao.publicacao_convenio
                            ON publicacao_convenio.num_convenio = publicacao_convenio.num_convenio
                        AND publicacao_convenio.exercicio = publicacao_convenio.exercicio

                    INNER JOIN sw_cgm AS cgm_imprensa
                            ON publicacao_convenio.numcgm = cgm_imprensa.numcgm

                    INNER JOIN licitacao.convenio_aditivos
                            ON convenio.exercicio = convenio_aditivos.exercicio_convenio
                        AND convenio.num_convenio = convenio_aditivos.num_convenio


                    WHERE NOT EXISTS (
                                        SELECT 1
                                        FROM licitacao.convenio_anulado
                                        WHERE convenio.num_convenio = convenio_anulado.num_convenio
                                            AND convenio.exercicio = convenio_anulado.exercicio
                                    )
                    AND NOT EXISTS (
                                        SELECT 1
                                        FROM licitacao.convenio_aditivos_anulacao
                                        WHERE convenio_aditivos.num_convenio = convenio_aditivos_anulacao.num_convenio
                                            AND convenio_aditivos.exercicio = convenio_aditivos_anulacao.exercicio
                                            AND convenio_aditivos.exercicio_convenio = convenio_aditivos_anulacao.exercicio_convenio
                                            AND convenio_aditivos.num_aditivo = convenio_aditivos_anulacao.num_aditivo
                                    )";

        return $stSql;
    }

}
