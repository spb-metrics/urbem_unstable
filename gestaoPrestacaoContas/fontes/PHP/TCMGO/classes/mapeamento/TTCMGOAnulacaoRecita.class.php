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
    * Extensão da Classe de mapeamento
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOAnulacaoRecita.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

class TTCMGOAnulacaoReceita extends TOrcamentoContaReceita
{

    public function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stCondicao .= " GROUP BY orgao_plano_banco.num_orgao, conta_receita.cod_estrutural, arrecadacao_estornada.observacao";
        $stSql = $this->montaRecuperaRelacionamento().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamento()
    {

        $stSQL = "    select '10'                                    as tipo_registro
                           , orgao_plano_banco.num_orgao             as cod_orgao
                           , '01' as num_unidade
                           , CASE WHEN substr(conta_receita.cod_estrutural::varchar, 1, 1)::integer = 9
                                THEN substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                                ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                             END as rubrica
                           , SUM(CASE WHEN arrecadacao_estornada.cod_arrecadacao IS NULL AND arrecadacao.devolucao = TRUE THEN
                                arrecadacao_receita.vl_arrecadacao
                             ELSE
                                arrecadacao_estornada_receita.vl_estornado
                             END) AS valor_estornado --realizada essa validacao para pegar as arrecadacoes que tenham devolucao = TRUE
                           , CASE WHEN (arrecadacao_estornada.observacao IS NOT NULL AND arrecadacao_estornada.observacao <> '')
                                  THEN substr(arrecadacao_estornada.observacao,1,255)
                                  ELSE 'Receita estornada'
                             END as justificativa
                      from orcamento.receita
                      join orcamento.conta_receita
                        on ( receita.exercicio = conta_receita.exercicio
                       and   receita.cod_conta = conta_receita.cod_conta )
                      join tesouraria.arrecadacao_receita
                        on ( receita.cod_receita = arrecadacao_receita.cod_receita
                       and   receita.exercicio   = arrecadacao_receita.exercicio)
                      join tesouraria.arrecadacao
                        on ( arrecadacao_receita.cod_arrecadacao       = arrecadacao.cod_arrecadacao
                       and   arrecadacao_receita.exercicio             = arrecadacao.exercicio
                       and   arrecadacao_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao )
                 left join tesouraria.arrecadacao_estornada
                        on ( arrecadacao.cod_arrecadacao      = arrecadacao_estornada.cod_arrecadacao
                       and  arrecadacao.exercicio             = arrecadacao_estornada.exercicio
                       and  arrecadacao.timestamp_arrecadacao = arrecadacao_estornada.timestamp_arrecadacao )
                 left join tesouraria.arrecadacao_estornada_receita
                        on ( arrecadacao_estornada.cod_arrecadacao      = arrecadacao_estornada_receita.cod_arrecadacao
                       and  arrecadacao_estornada.exercicio             = arrecadacao_estornada_receita.exercicio
                       and  arrecadacao_estornada.timestamp_arrecadacao = arrecadacao_estornada_receita.timestamp_arrecadacao
                       and  arrecadacao_estornada.timestamp_estornada   = arrecadacao_estornada_receita.timestamp_estornada )
                      join  contabilidade.plano_analitica
                        on ( arrecadacao.cod_plano = plano_analitica.cod_plano
                       and   arrecadacao.exercicio = plano_analitica.exercicio )
                      join tcmgo.orgao_plano_banco
                        on ( plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       and   plano_analitica.exercicio = orgao_plano_banco.exercicio )
                     where ( arrecadacao.devolucao = TRUE OR arrecadacao_estornada.cod_arrecadacao IS NOT NULL) --realizada essa validacao para pegar as arrecadacoes que tenham devolucao = TRUE
                       and arrecadacao_receita.timestamp_arrecadacao::date  >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                       and arrecadacao_receita.timestamp_arrecadacao::date  <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       and receita.exercicio = '". Sessao::getExercicio() . "'";

                 if ( $this->getDado ( 'stEntidades' ) ) {
                    $stSQL .= "\n  and receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ") ";
                 }

        return $stSQL;
    }

    public function recuperaDetalhamentoConta(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamentoConta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamentoConta()
    {

        $stSQL = "
             SELECT tipo_registro
                 , cod_orgao
                 , '01' as num_unidade
                 , rubrica
                 , cod_receita
                 , exercicio
                 , vl_estornado
                 , fonte
                 , banco
                 , agencia
                 , conta_corrente
                 , digito
                 , tipo_conta
              FROM (
                    SELECT '11'                             AS tipo_registro
                         , orgao_plano_banco.num_orgao      AS cod_orgao
                         , CASE
                            WHEN substr(conta_receita.cod_estrutural::varchar, 1, 1)::integer = 9 THEN
                                       substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                            ELSE
                                '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END                              AS rubrica
                         , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1') THEN
                                '03'
                                WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                                   OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                                   OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4')) THEN
                                     '02'
                                ELSE
                                     '01'
                           END as tipo_conta
                         , receita.cod_receita
                         , receita.exercicio
                         --, ABS(SUM(arrecadacao_estornada_receita.vl_estornado)) AS vl_estornado
                         , SUM(CASE WHEN arrecadacao_estornada.cod_arrecadacao IS NULL AND arrecadacao.devolucao = TRUE THEN
                                arrecadacao_receita.vl_arrecadacao
                             ELSE
                                arrecadacao_estornada_receita.vl_estornado
                             END) AS vl_estornado --realizada essa validacao para pegar as arrecadacoes que tenham devolucao = TRUE
                         , banco.num_banco AS banco
                         , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                         , CASE
                           WHEN  banco.num_banco = '999' THEN '999999999999'
                           ELSE ltrim(split_part(num_conta_corrente,'-',1),'0')
                           END AS conta_corrente
                         , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
                         , recurso.cod_fonte as fonte
                      FROM orcamento.receita
                      JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                      JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                      JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao
                 LEFT JOIN tesouraria.arrecadacao_estornada
                        ON ( arrecadacao.cod_arrecadacao      = arrecadacao_estornada.cod_arrecadacao
                       AND  arrecadacao.exercicio             = arrecadacao_estornada.exercicio
                       AND  arrecadacao.timestamp_arrecadacao = arrecadacao_estornada.timestamp_arrecadacao )
                 LEFT JOIN tesouraria.arrecadacao_estornada_receita
                        ON ( arrecadacao_estornada.cod_arrecadacao      = arrecadacao_estornada_receita.cod_arrecadacao
                       AND  arrecadacao_estornada.exercicio             = arrecadacao_estornada_receita.exercicio
                       AND  arrecadacao_estornada.timestamp_arrecadacao = arrecadacao_estornada_receita.timestamp_arrecadacao
                       AND  arrecadacao_estornada.timestamp_estornada   = arrecadacao_estornada_receita.timestamp_estornada )
                      JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                      JOIN orcamento.recurso
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio   = receita.exercicio
                      JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                      JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                      JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                      JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                      JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                      JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio
                        -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                      JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                      JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                            --  AND NOT EXISTS ( SELECT 1
                            --                     FROM tesouraria.boletim_liberado_cancelado
                            --                    WHERE boletim_liberado_cancelado.cod_boletim          = boletim_liberado_cancelado.cod_boletim
                            --                      AND boletim_liberado_cancelado.cod_entidade         = boletim_liberado_cancelado.cod_entidade
                            --                      AND boletim_liberado_cancelado.exercicio            = boletim_liberado_cancelado.exercicio
                            --                      AND boletim_liberado_cancelado.timestamp_fechamento = boletim_liberado_cancelado.timestamp_fechamento   )
                           )                           AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                     WHERE ( arrecadacao.devolucao = TRUE OR arrecadacao_estornada.cod_arrecadacao IS NOT NULL) --realizada essa validacao para pegar as arrecadacoes que tenham devolucao = TRUE
                       AND to_date(arrecadacao_receita.timestamp_arrecadacao,'yyyy-mm-dd') BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       --AND arrecadacao.devolucao = FALSE
                     AND receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ")
                  GROUP BY tipo_registro
                         , cod_orgao
                         , 1
                         , receita.cod_receita
                         , rubrica
                         , receita.exercicio
                         , banco.num_banco
                         , agencia.num_agencia
                         , conta_corrente.num_conta_corrente
                         , recurso.cod_fonte
                         , plano_conta.cod_estrutural
            ) AS tabela
     GROUP BY tipo_registro
            , cod_orgao
            , 1
            , rubrica
            , cod_receita
            , exercicio
            , vl_estornado
            , fonte
            , banco
            , agencia
            , conta_corrente
            , digito
            , tipo_conta
    ";

            return $stSQL;

    }

    public function recuperaDetalhamentoFonteRecurso(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamentoFonteRecurso",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamentoFonteRecurso()
    {

        $stSQL = "
             SELECT tipo_registro
                 , cod_orgao
                 , '01' as num_unidade
                 , rubrica
                 , cod_receita
                 , exercicio
                 , vl_estornado
                 , fonte
                 , banco
                 , agencia
                 , conta_corrente
                 , digito
                 , tipo_conta
              FROM (
                    SELECT '12'                             AS tipo_registro
                         , orgao_plano_banco.num_orgao      AS cod_orgao
                         , CASE
                            WHEN substr(conta_receita.cod_estrutural::varchar, 1, 1)::integer = 9 THEN
                                       substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                            ELSE
                                '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END                              AS rubrica
                         , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1') THEN
                                '03'
                                WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                                   OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                                   OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4')) THEN
                                     '02'
                                ELSE
                                     '01'
                           END as tipo_conta
                         , receita.cod_receita
                         , receita.exercicio
                         , conta_receita.descricao
                         --, ABS(SUM(arrecadacao_estornada_receita.vl_estornado)) AS vl_estornado
                         , SUM(CASE WHEN arrecadacao_estornada.cod_arrecadacao IS NULL AND arrecadacao.devolucao = TRUE THEN
                                arrecadacao_receita.vl_arrecadacao
                             ELSE
                                arrecadacao_estornada_receita.vl_estornado
                             END) AS vl_estornado --realizada essa validacao para pegar as arrecadacoes que tenham devolucao = TRUE
                         , banco.num_banco AS banco
                         , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                         , CASE
                           WHEN  banco.num_banco = '999' THEN '999999999999'
                           ELSE ltrim(split_part(num_conta_corrente,'-',1),'0')
                           END AS conta_corrente
                         , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
                         , recurso.cod_fonte as fonte
                      FROM orcamento.receita
                      JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                      JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                      JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao
                 LEFT JOIN tesouraria.arrecadacao_estornada
                        ON ( arrecadacao.cod_arrecadacao      = arrecadacao_estornada.cod_arrecadacao
                       AND  arrecadacao.exercicio             = arrecadacao_estornada.exercicio
                       AND  arrecadacao.timestamp_arrecadacao = arrecadacao_estornada.timestamp_arrecadacao )
                 LEFT JOIN tesouraria.arrecadacao_estornada_receita
                        ON ( arrecadacao_estornada.cod_arrecadacao      = arrecadacao_estornada_receita.cod_arrecadacao
                       AND  arrecadacao_estornada.exercicio             = arrecadacao_estornada_receita.exercicio
                       AND  arrecadacao_estornada.timestamp_arrecadacao = arrecadacao_estornada_receita.timestamp_arrecadacao
                       AND  arrecadacao_estornada.timestamp_estornada   = arrecadacao_estornada_receita.timestamp_estornada )
                      JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                      JOIN orcamento.recurso
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio   = receita.exercicio
                      JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                      JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                      JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                      JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                      JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                      JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio
                        -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                      JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                      JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                           ) AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                     WHERE ( arrecadacao.devolucao = TRUE OR arrecadacao_estornada.cod_arrecadacao IS NOT NULL) --realizada essa validacao para pegar as arrecadacoes que tenham devolucao = TRUE
                       AND to_date(arrecadacao_receita.timestamp_arrecadacao,'yyyy-mm-dd') BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       --AND arrecadacao.devolucao = FALSE
                     AND receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ")
                  GROUP BY tipo_registro
                         , cod_orgao
                         , 1
                         , receita.cod_receita
                         , rubrica
                         , receita.exercicio
                         , conta_receita.descricao
                         , vl_estornado
                         , banco.num_banco
                         , agencia.num_agencia
                         , conta_corrente.num_conta_corrente
                         , recurso.cod_fonte
                         , plano_conta.cod_estrutural
            ) AS tabela
     GROUP BY tipo_registro
            , cod_orgao
            , 1
            , rubrica
            , cod_receita
            , exercicio
            , vl_estornado
            , fonte
            , banco
            , agencia
            , conta_corrente
            , digito
            , tipo_conta
    ";

        return $stSQL;
    }

}

?>
