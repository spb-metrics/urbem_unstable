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
 * Mapeamento da tabela tesouraria.cheque
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TTesourariaCheque extends Persistente
{
    /**
     * Método Construtor da classe TTesourariaCheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tesouraria.cheque');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_agencia, cod_banco, cod_conta_corrente, num_cheque');

        $this->AddCampo('cod_agencia'        ,'integer', true, ''  , true, true );
        $this->AddCampo('cod_banco'          ,'integer', true, ''  , true, true );
        $this->AddCampo('cod_conta_corrente' ,'integer', true, ''  , true, true );
        $this->AddCampo('num_cheque'         ,'varchar', true, '15', true, false);
    }

    /**
     * Método que retorna os cheques vinculados a conta, agencia e banco
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function getCheque(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("buildGetCheque",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Método que constroi a string SQL para o metodo getCheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return string Consulta SQL
     */
    public function buildGetCheque()
    {
        $stSql = "
            SELECT cheque.num_cheque
                 , conta_corrente.cod_conta_corrente
                 , conta_corrente.num_conta_corrente
                 , agencia.cod_agencia
                 , agencia.num_agencia
                 , agencia.nom_agencia
                 , banco.cod_banco
                 , banco.num_banco
                 , banco.nom_banco
                 , CASE WHEN cheque_emissao.num_cheque IS NULL
                        THEN 'Não'
                        ELSE CASE WHEN cheque_emissao_anulada.num_cheque IS NULL
                                  THEN 'Sim'
                                  ELSE 'Anulado'
                             END
                   END AS emitido
              FROM tesouraria.cheque
        INNER JOIN monetario.conta_corrente
                ON cheque.cod_conta_corrente  = conta_corrente.cod_conta_corrente
               AND cheque.cod_agencia         = conta_corrente.cod_agencia
               AND cheque.cod_banco           = conta_corrente.cod_banco
        INNER JOIN monetario.agencia
                ON conta_corrente.cod_agencia = agencia.cod_agencia
               AND conta_corrente.cod_banco   = agencia.cod_banco
        INNER JOIN monetario.banco
                ON agencia.cod_banco          = banco.cod_banco

         LEFT JOIN ( SELECT cheque_emissao.cod_banco
                          , cheque_emissao.cod_agencia
                          , cheque_emissao.cod_conta_corrente
                          , cheque_emissao.num_cheque
                          , cheque_emissao.timestamp_emissao
                       FROM tesouraria.cheque_emissao
                 INNER JOIN ( SELECT cheque_emissao.cod_banco
                                   , cheque_emissao.cod_agencia
                                   , cheque_emissao.cod_conta_corrente
                                   , cheque_emissao.num_cheque
                                   , MAX(cheque_emissao.timestamp_emissao) AS timestamp_emissao
                                FROM tesouraria.cheque_emissao
                            GROUP BY cheque_emissao.cod_banco
                                   , cheque_emissao.cod_agencia
                                   , cheque_emissao.cod_conta_corrente
                                   , cheque_emissao.num_cheque
                            ) AS cheque_emissao_max
                         ON cheque_emissao.cod_banco          = cheque_emissao_max.cod_banco
                        AND cheque_emissao.cod_agencia        = cheque_emissao_max.cod_agencia
                        AND cheque_emissao.cod_conta_corrente = cheque_emissao_max.cod_conta_corrente
                        AND cheque_emissao.num_cheque         = cheque_emissao_max.num_cheque
                        AND cheque_emissao.timestamp_emissao  = cheque_emissao_max.timestamp_emissao
                   ) AS cheque_emissao
                ON cheque.cod_banco          = cheque_emissao.cod_banco
               AND cheque.cod_agencia        = cheque_emissao.cod_agencia
               AND cheque.cod_conta_corrente = cheque_emissao.cod_conta_corrente
               AND cheque.num_cheque         = cheque_emissao.num_cheque

         LEFT JOIN tesouraria.cheque_emissao_ordem_pagamento
                ON cheque_emissao.cod_banco          = cheque_emissao_ordem_pagamento.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_ordem_pagamento.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_ordem_pagamento.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_ordem_pagamento.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_ordem_pagamento.timestamp_emissao

         LEFT JOIN ( SELECT cheque_emissao_transferencia.cod_banco
                          , cheque_emissao_transferencia.cod_agencia
                          , cheque_emissao_transferencia.cod_conta_corrente
                          , cheque_emissao_transferencia.num_cheque
                          , cheque_emissao_transferencia.timestamp_emissao
                          , transferencia.exercicio
                          , transferencia.cod_entidade
                          , transferencia.cod_plano_credito
                          , transferencia.cod_plano_debito
                          , transferencia.cod_tipo
                       FROM tesouraria.cheque_emissao_transferencia
                 INNER JOIN tesouraria.transferencia
                         ON cheque_emissao_transferencia.cod_lote     = transferencia.cod_lote
                        AND cheque_emissao_transferencia.cod_entidade = transferencia.cod_entidade
                        AND cheque_emissao_transferencia.exercicio    = transferencia.exercicio
                        AND cheque_emissao_transferencia.tipo         = transferencia.tipo

                   ) AS cheque_emissao_transferencia
                ON cheque_emissao.cod_banco          = cheque_emissao_transferencia.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_transferencia.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_transferencia.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_transferencia.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_transferencia.timestamp_emissao

         LEFT JOIN ( SELECT cheque_emissao_recibo_extra.cod_banco
                          , cheque_emissao_recibo_extra.cod_agencia
                          , cheque_emissao_recibo_extra.cod_conta_corrente
                          , cheque_emissao_recibo_extra.num_cheque
                          , cheque_emissao_recibo_extra.timestamp_emissao
                          , recibo_extra.timestamp
                          , recibo_extra.cod_plano
                          , cheque_emissao_recibo_extra.cod_recibo_extra
                          , cheque_emissao_recibo_extra.cod_entidade
                          , cheque_emissao_recibo_extra.exercicio
                       FROM tesouraria.cheque_emissao_recibo_extra
                 INNER JOIN tesouraria.recibo_extra
                         ON cheque_emissao_recibo_extra.cod_recibo_extra = recibo_extra.cod_recibo_extra
                        AND cheque_emissao_recibo_extra.cod_entidade     = recibo_extra.cod_entidade
                        AND cheque_emissao_recibo_extra.exercicio        = recibo_extra.exercicio
                        AND cheque_emissao_recibo_extra.tipo_recibo      = recibo_extra.tipo_recibo

                   ) AS cheque_emissao_recibo_extra
                ON cheque_emissao.cod_banco          = cheque_emissao_recibo_extra.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_recibo_extra.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_recibo_extra.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_recibo_extra.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_recibo_extra.timestamp_emissao
         LEFT JOIN tesouraria.cheque_emissao_anulada
                ON cheque_emissao.cod_banco          = cheque_emissao_anulada.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_anulada.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_anulada.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_anulada.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_anulada.timestamp_emissao
         LEFT JOIN ( SELECT cheque_emissao_baixa.cod_banco
                          , cheque_emissao_baixa.cod_agencia
                          , cheque_emissao_baixa.cod_conta_corrente
                          , cheque_emissao_baixa.num_cheque
                          , cheque_emissao_baixa.timestamp_emissao
                       FROM tesouraria.cheque_emissao_baixa
                 INNER JOIN ( SELECT cheque_emissao_baixa.cod_banco
                                   , cheque_emissao_baixa.cod_agencia
                                   , cheque_emissao_baixa.cod_conta_corrente
                                   , cheque_emissao_baixa.num_cheque
                                   , cheque_emissao_baixa.timestamp_emissao
                                   , MAX(cheque_emissao_baixa.timestamp_baixa) AS timestamp_baixa
                                FROM tesouraria.cheque_emissao_baixa
                            GROUP BY cheque_emissao_baixa.cod_banco
                                   , cheque_emissao_baixa.cod_agencia
                                   , cheque_emissao_baixa.cod_conta_corrente
                                   , cheque_emissao_baixa.num_cheque
                                   , cheque_emissao_baixa.timestamp_emissao
                            ) AS cheque_emissao_baixa_max
                         ON cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_max.cod_banco
                        AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_max.cod_agencia
                        AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_max.cod_conta_corrente
                        AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_max.num_cheque
                        AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_max.timestamp_emissao
                        AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_max.timestamp_baixa
                      WHERE NOT EXISTS ( SELECT 1
                                           FROM tesouraria.cheque_emissao_baixa_anulada
                                          WHERE cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_anulada.cod_banco
                                            AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_anulada.cod_agencia
                                            AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_anulada.cod_conta_corrente
                                            AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_anulada.num_cheque
                                            AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_anulada.timestamp_emissao
                                            AND cheque_emissao_baixa.timestamp_baixa  = cheque_emissao_baixa_anulada.timestamp_baixa
                                       )
                    ) AS cheque_emissao_baixa
                 ON cheque_emissao.cod_banco          = cheque_emissao_baixa.cod_banco
                AND cheque_emissao.cod_agencia        = cheque_emissao_baixa.cod_agencia
                AND cheque_emissao.cod_conta_corrente = cheque_emissao_baixa.cod_conta_corrente
                AND cheque_emissao.num_cheque         = cheque_emissao_baixa.num_cheque
                AND cheque_emissao.timestamp_emissao  = cheque_emissao_baixa.timestamp_emissao
        ";

        return $stSql;
    }

    /**
     * Método que constroi a string SQL para o metodo getCheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function getChequeAnalitico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT cheque.num_cheque
                 , conta_corrente.cod_conta_corrente
                 , conta_corrente.num_conta_corrente
                 , agencia.cod_agencia
                 , agencia.num_agencia
                 , agencia.nom_agencia
                 , banco.cod_banco
                 , banco.num_banco
                 , banco.nom_banco
                 , TO_CHAR(cheque.data_entrada,'dd/mm/yyyy') AS data_entrada
                 , CASE WHEN cheque_emissao.num_cheque IS NULL
                        THEN 'Não'
                        ELSE CASE WHEN cheque_emissao_anulada.num_cheque IS NULL
                                  THEN 'Sim'
                                  ELSE 'Anulado'
                             END
                   END AS emitido
                 , cheque_emissao.valor
                 , TO_CHAR(cheque_emissao_anulada.data_anulacao,'dd/mm/yyyy') AS data_anulacao
                 , TO_CHAR(cheque_emissao.data_emissao,'dd/mm/yyyy') AS data_emissao
                 , cheque_emissao.descricao
                 , CASE WHEN (cheque_emissao_ordem_pagamento.cod_ordem IS NOT NULL)
                        THEN cheque_emissao_ordem_pagamento.exercicio
                        WHEN (cheque_emissao_recibo_extra.cod_recibo_extra IS NOT NULL)
                        THEN cheque_emissao_recibo_extra.exercicio
                        ELSE cheque_emissao_transferencia.exercicio
                   END AS exercicio
                 , CASE WHEN (cheque_emissao_ordem_pagamento.cod_ordem IS NOT NULL)
                        THEN cheque_emissao_ordem_pagamento.cod_entidade
                        WHEN (cheque_emissao_recibo_extra.cod_recibo_extra IS NOT NULL)
                        THEN cheque_emissao_recibo_extra.cod_entidade
                        ELSE cheque_emissao_transferencia.cod_entidade
                   END AS cod_entidade
                 , CASE WHEN (cheque_emissao_ordem_pagamento.cod_ordem IS NOT NULL)
                        THEN cheque_emissao_ordem_pagamento.nom_entidade
                        WHEN (cheque_emissao_recibo_extra.cod_recibo_extra IS NOT NULL)
                        THEN cheque_emissao_recibo_extra.nom_entidade
                        ELSE cheque_emissao_transferencia.nom_entidade
                   END AS nom_entidade
                 , cheque_emissao_ordem_pagamento.cod_ordem
                 , cheque_emissao_recibo_extra.cod_recibo_extra
                 , cheque_emissao_transferencia.cod_plano_credito
                 , cheque_emissao_transferencia.nom_plano_credito
                 , cheque_emissao_transferencia.cod_plano_debito
                 , cheque_emissao_transferencia.nom_plano_debito
                 , CASE WHEN (cheque_emissao_recibo_extra.cod_recibo_extra IS NOT NULL)
                        THEN 'despesa_extra'
                        WHEN (cheque_emissao_transferencia.cod_tipo IS NOT NULL)
                        THEN 'transferencia'
                        WHEN (cheque_emissao_ordem_pagamento.cod_ordem IS NOT NULL)
                        THEN 'ordem_pagamento'
                        ELSE ''
                   END AS tipo_emissao
                 , CASE WHEN (cheque_emissao_baixa.num_cheque IS NOT NULL)
                        THEN 'Sim'
                        ELSE 'Não'
                   END AS cheque_baixado
                 , CASE WHEN (cheque_emissao_recibo_extra.data_baixa IS NOT NULL)
                        THEN cheque_emissao_recibo_extra.data_baixa
                        WHEN (cheque_emissao_transferencia.data_baixa IS NOT NULL)
                        THEN cheque_emissao_transferencia.data_baixa
                        WHEN (cheque_emissao_ordem_pagamento.data_baixa IS NOT NULL)
                        THEN cheque_emissao_ordem_pagamento.data_baixa
                        ELSE ''
                   END AS data_baixa
              FROM tesouraria.cheque
        INNER JOIN monetario.conta_corrente
                ON cheque.cod_conta_corrente  = conta_corrente.cod_conta_corrente
               AND cheque.cod_agencia         = conta_corrente.cod_agencia
               AND cheque.cod_banco           = conta_corrente.cod_banco
        INNER JOIN monetario.agencia
                ON conta_corrente.cod_agencia = agencia.cod_agencia
               AND conta_corrente.cod_banco   = agencia.cod_banco
        INNER JOIN monetario.banco
                ON agencia.cod_banco          = banco.cod_banco

         LEFT JOIN tesouraria.cheque_emissao
                ON cheque.cod_banco          = cheque_emissao.cod_banco
               AND cheque.cod_agencia        = cheque_emissao.cod_agencia
               AND cheque.cod_conta_corrente = cheque_emissao.cod_conta_corrente
               AND cheque.num_cheque         = cheque_emissao.num_cheque

         LEFT JOIN ( SELECT cheque_emissao_ordem_pagamento.cod_banco
                          , cheque_emissao_ordem_pagamento.cod_agencia
                          , cheque_emissao_ordem_pagamento.cod_conta_corrente
                          , cheque_emissao_ordem_pagamento.num_cheque
                          , cheque_emissao_ordem_pagamento.timestamp_emissao
                          , entidade.cod_entidade
                          , sw_cgm.nom_cgm AS nom_entidade
                          , cheque_emissao_ordem_pagamento.cod_ordem
                          , cheque_emissao_ordem_pagamento.exercicio
                          , data_baixa
                       FROM tesouraria.cheque_emissao_ordem_pagamento
                 INNER JOIN orcamento.entidade
                         ON cheque_emissao_ordem_pagamento.cod_entidade = entidade.cod_entidade
                        AND cheque_emissao_ordem_pagamento.exercicio    = entidade.exercicio
                 INNER JOIN sw_cgm
                         ON entidade.numcgm = sw_cgm.numcgm
                  LEFT JOIN ( SELECT pagamento_liquidacao.exercicio
                                   , pagamento_liquidacao.cod_entidade
                                   , pagamento_liquidacao.cod_ordem
                                   , data_baixa
                                FROM empenho.pagamento_liquidacao
                          INNER JOIN ( SELECT nota_liquidacao_paga.cod_nota
                                            , nota_liquidacao_paga.cod_entidade
                                            , nota_liquidacao_paga.exercicio
                                            , TO_CHAR(MAX(nota_liquidacao_paga.timestamp),'dd/mm/yyyy') AS data_baixa
                                         FROM empenho.nota_liquidacao_paga
                                        WHERE NOT EXISTS ( SELECT 1
                                                             FROM empenho.nota_liquidacao_paga_anulada
                                                            WHERE nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade
                                                              AND nota_liquidacao_paga.cod_nota     = nota_liquidacao_paga_anulada.cod_nota
                                                              AND nota_liquidacao_paga.exercicio    = nota_liquidacao_paga_anulada.exercicio
                                                              AND nota_liquidacao_paga.timestamp    = nota_liquidacao_paga_anulada.timestamp
                                                          )
                                     GROUP BY nota_liquidacao_paga.cod_nota
                                            , nota_liquidacao_paga.cod_entidade
                                            , nota_liquidacao_paga.exercicio
                                     ) AS nota_liquidacao_paga
                                  ON pagamento_liquidacao.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                 AND pagamento_liquidacao.cod_nota             = nota_liquidacao_paga.cod_nota
                                 AND pagamento_liquidacao.cod_entidade         = nota_liquidacao_paga.cod_entidade
                            GROUP BY pagamento_liquidacao.exercicio
                                   , pagamento_liquidacao.cod_entidade
                                   , pagamento_liquidacao.cod_ordem
                                   , data_baixa
                            ) AS pagamento_liquidacao
                         ON cheque_emissao_ordem_pagamento.exercicio    = pagamento_liquidacao.exercicio
                        AND cheque_emissao_ordem_pagamento.cod_entidade = pagamento_liquidacao.cod_entidade
                        AND cheque_emissao_ordem_pagamento.cod_ordem    = pagamento_liquidacao.cod_ordem

                   ) AS cheque_emissao_ordem_pagamento
                ON cheque_emissao.cod_banco          = cheque_emissao_ordem_pagamento.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_ordem_pagamento.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_ordem_pagamento.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_ordem_pagamento.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_ordem_pagamento.timestamp_emissao

         LEFT JOIN ( SELECT cheque_emissao_recibo_extra.cod_banco
                          , cheque_emissao_recibo_extra.cod_agencia
                          , cheque_emissao_recibo_extra.cod_conta_corrente
                          , cheque_emissao_recibo_extra.num_cheque
                          , cheque_emissao_recibo_extra.timestamp_emissao
                          , entidade.cod_entidade
                          , sw_cgm.nom_cgm AS nom_entidade
                          , cheque_emissao_recibo_extra.cod_recibo_extra
                          , cheque_emissao_recibo_extra.exercicio
                          , TO_CHAR(boletim.dt_boletim,'dd/mm/yyyy') AS data_baixa
                       FROM tesouraria.cheque_emissao_recibo_extra
                 INNER JOIN orcamento.entidade
                         ON cheque_emissao_recibo_extra.cod_entidade = entidade.cod_entidade
                        AND cheque_emissao_recibo_extra.exercicio    = entidade.exercicio
                 INNER JOIN sw_cgm
                         ON entidade.numcgm = sw_cgm.numcgm
                  LEFT JOIN tesouraria.recibo_extra_transferencia
                         ON cheque_emissao_recibo_extra.cod_recibo_extra = recibo_extra_transferencia.cod_recibo_extra
                        AND cheque_emissao_recibo_extra.exercicio        = recibo_extra_transferencia.exercicio
                        AND cheque_emissao_recibo_extra.cod_entidade     = recibo_extra_transferencia.cod_entidade
                        AND cheque_emissao_recibo_extra.tipo_recibo      = recibo_extra_transferencia.tipo_recibo
                  LEFT JOIN tesouraria.transferencia
                         ON recibo_extra_transferencia.cod_lote     = transferencia.cod_lote
                        AND recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
                        AND recibo_extra_transferencia.exercicio    = transferencia.exercicio
                        AND recibo_extra_transferencia.tipo         = transferencia.tipo
                  LEFT JOIN tesouraria.boletim
                         ON transferencia.cod_boletim  = boletim.cod_boletim
                        AND transferencia.cod_entidade = boletim.cod_entidade
                        AND transferencia.exercicio    = boletim.exercicio
                   ) AS cheque_emissao_recibo_extra
                ON cheque_emissao.cod_banco          = cheque_emissao_recibo_extra.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_recibo_extra.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_recibo_extra.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_recibo_extra.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_recibo_extra.timestamp_emissao

         LEFT JOIN ( SELECT cheque_emissao_transferencia.cod_banco
                          , cheque_emissao_transferencia.cod_agencia
                          , cheque_emissao_transferencia.cod_conta_corrente
                          , cheque_emissao_transferencia.num_cheque
                          , cheque_emissao_transferencia.timestamp_emissao
                          , entidade.cod_entidade
                          , sw_cgm.nom_cgm AS nom_entidade
                          , transferencia.exercicio
                          , transferencia.cod_plano_credito
                          , plano_conta_credito.nom_conta AS nom_plano_credito
                          , transferencia.cod_plano_debito
                          , plano_conta_debito.nom_conta AS nom_plano_debito
                          , transferencia.cod_tipo
                          , TO_CHAR(boletim.dt_boletim,'dd/mm/yyyy') AS data_baixa
                       FROM tesouraria.cheque_emissao_transferencia
                 INNER JOIN tesouraria.transferencia
                         ON cheque_emissao_transferencia.cod_lote     = transferencia.cod_lote
                        AND cheque_emissao_transferencia.cod_entidade = transferencia.cod_entidade
                        AND cheque_emissao_transferencia.exercicio    = transferencia.exercicio
                        AND cheque_emissao_transferencia.tipo         = transferencia.tipo
                 INNER JOIN orcamento.entidade
                         ON transferencia.cod_entidade = entidade.cod_entidade
                        AND transferencia.exercicio    = entidade.exercicio
                 INNER JOIN sw_cgm
                         ON entidade.numcgm = sw_cgm.numcgm

                 INNER JOIN contabilidade.plano_analitica AS plano_analitica_debito
                         ON transferencia.cod_plano_debito = plano_analitica_debito.cod_plano
                        AND transferencia.exercicio        = plano_analitica_debito.exercicio
                 INNER JOIN contabilidade.plano_conta AS plano_conta_debito
                         ON plano_analitica_debito.cod_conta = plano_conta_debito.cod_conta
                        AND plano_analitica_debito.exercicio = plano_conta_debito.exercicio

                 INNER JOIN contabilidade.plano_analitica AS plano_analitica_credito
                         ON transferencia.cod_plano_credito = plano_analitica_credito.cod_plano
                        AND transferencia.exercicio         = plano_analitica_credito.exercicio
                 INNER JOIN contabilidade.plano_conta AS plano_conta_credito
                         ON plano_analitica_credito.cod_conta = plano_conta_credito.cod_conta
                        AND plano_analitica_credito.exercicio = plano_conta_credito.exercicio
                  LEFT JOIN tesouraria.boletim
                         ON transferencia.cod_boletim  = boletim.cod_boletim
                        AND transferencia.cod_entidade = boletim.cod_entidade
                        AND transferencia.exercicio    = boletim.exercicio
                   ) AS cheque_emissao_transferencia
                ON cheque_emissao.cod_banco          = cheque_emissao_transferencia.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_transferencia.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_transferencia.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_transferencia.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_transferencia.timestamp_emissao
         LEFT JOIN tesouraria.cheque_emissao_anulada
                ON cheque_emissao.cod_banco          = cheque_emissao_anulada.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_anulada.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_anulada.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_anulada.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_anulada.timestamp_emissao
         LEFT JOIN ( SELECT cheque_emissao_baixa.cod_banco
                          , cheque_emissao_baixa.cod_agencia
                          , cheque_emissao_baixa.cod_conta_corrente
                          , cheque_emissao_baixa.num_cheque
                          , cheque_emissao_baixa.timestamp_emissao
                       FROM tesouraria.cheque_emissao_baixa
                 INNER JOIN ( SELECT cheque_emissao_baixa.cod_banco
                                   , cheque_emissao_baixa.cod_agencia
                                   , cheque_emissao_baixa.cod_conta_corrente
                                   , cheque_emissao_baixa.num_cheque
                                   , cheque_emissao_baixa.timestamp_emissao
                                   , MAX(cheque_emissao_baixa.timestamp_baixa) AS timestamp_baixa
                                FROM tesouraria.cheque_emissao_baixa
                            GROUP BY cheque_emissao_baixa.cod_banco
                                   , cheque_emissao_baixa.cod_agencia
                                   , cheque_emissao_baixa.cod_conta_corrente
                                   , cheque_emissao_baixa.num_cheque
                                   , cheque_emissao_baixa.timestamp_emissao
                            ) AS cheque_emissao_baixa_max
                         ON cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_max.cod_banco
                        AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_max.cod_agencia
                        AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_max.cod_conta_corrente
                        AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_max.num_cheque
                        AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_max.timestamp_emissao
                        AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_max.timestamp_baixa
                      WHERE NOT EXISTS ( SELECT 1
                                           FROM tesouraria.cheque_emissao_baixa_anulada
                                          WHERE cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_anulada.cod_banco
                                            AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_anulada.cod_agencia
                                            AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_anulada.cod_conta_corrente
                                            AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_anulada.num_cheque
                                            AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_anulada.timestamp_emissao
                                            AND cheque_emissao_baixa.timestamp_baixa  = cheque_emissao_baixa_anulada.timestamp_baixa
                                       )
                    ) AS cheque_emissao_baixa
                 ON cheque_emissao.cod_banco          = cheque_emissao_baixa.cod_banco
                AND cheque_emissao.cod_agencia        = cheque_emissao_baixa.cod_agencia
                AND cheque_emissao.cod_conta_corrente = cheque_emissao_baixa.cod_conta_corrente
                AND cheque_emissao.num_cheque         = cheque_emissao_baixa.num_cheque
                AND cheque_emissao.timestamp_emissao  = cheque_emissao_baixa.timestamp_emissao
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
