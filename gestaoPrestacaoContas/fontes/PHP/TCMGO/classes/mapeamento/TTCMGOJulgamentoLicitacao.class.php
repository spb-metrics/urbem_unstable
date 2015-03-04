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
    * Classe de mapeamento da tabela licitacao.participante
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoParticipante.class.php 57380 2014-02-28 17:45:35Z diogo.zarpelon $

    * Casos de uso: uc-03.05.18
            uc-03.05.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Thiago La Delfa Cabelleira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTCMGOJulgamentoLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaExportacao10(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaExportacao10", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }
    
    public function montaRecuperaExportacao10()
    {
        $stSql = "  SELECT
                          10 AS tipo_registro
                        , LPAD(licitacao.num_orgao::VARCHAR,2,'0') AS cod_orgao
                        , LPAD(licitacao.num_unidade::VARCHAR, 2, '0') AS cod_unidade
                        , licitacao.exercicio AS exercicio_licitacao
                        , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                        , documento_pessoa.tipo_documento AS tipo_documento
                        , documento_pessoa.num_documento AS num_documento
                        , mapa_item.lote AS num_lote
                        , mapa_item.cod_item AS cod_item
                        , catalogo_item.descricao::VARCHAR(250) AS dsc_produto_servico
                        , (mapa_item.vl_total / mapa_item.quantidade)::numeric(14,2) AS vl_unitario
                        , mapa_item.quantidade::numeric(14,2) AS quantidade
                        , CASE CONCAT(unidade_medida.cod_unidade,unidade_medida.cod_grandeza)
                                WHEN '00' THEN 1
                                WHEN '17' THEN 1
                                WHEN '67' THEN 1
                                WHEN '18' THEN 2
                                WHEN '34' THEN 11
                                WHEN '44' THEN 12
                                WHEN '12' THEN 21
                                WHEN '22' THEN 22
                                WHEN '32' THEN 23
                                WHEN '25' THEN 31
                                WHEN '15' THEN 32
                                WHEN '23' THEN 41
                                WHEN '33' THEN 42
                                WHEN '31' THEN 51
                                WHEN '41' THEN 52
                                WHEN '51' THEN 53
                                WHEN '61' THEN 54
                                ELSE 1                                 
                        END AS unidade
                          
                    FROM licitacao.licitacao
                    
                    JOIN licitacao.participante
                         ON participante.cod_licitacao  = licitacao.cod_licitacao
                        AND participante.cod_modalidade = licitacao.cod_modalidade
                        AND participante.cod_entidade   = licitacao.cod_entidade 
                        AND participante.exercicio      = licitacao.exercicio
                    
                    JOIN compras.mapa
                         ON mapa.exercicio = licitacao.exercicio_mapa
                        AND mapa.cod_mapa  = licitacao.cod_mapa
                     
                    JOIN compras.mapa_solicitacao
                         ON mapa_solicitacao.exercicio = mapa.exercicio
                        AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
                     
                    JOIN compras.mapa_item
                         ON mapa_item.exercicio             = mapa_solicitacao.exercicio
                        AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                        AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                        AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                        AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                    
                    JOIN compras.mapa_cotacao
                         ON mapa_cotacao.exercicio_mapa = mapa.exercicio
                        AND mapa_cotacao.cod_mapa       = mapa.cod_mapa
                     
                    JOIN compras.cotacao
                         ON cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
                        AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

                    JOIN compras.cotacao_item
                         ON cotacao_item.exercicio   = cotacao.exercicio
                        AND cotacao_item.cod_cotacao = cotacao.cod_cotacao
                        AND cotacao_item.cod_item    = mapa_item.cod_item

                    JOIN almoxarifado.catalogo_item
                        ON catalogo_item.cod_item = cotacao_item.cod_item

                    JOIN administracao.unidade_medida
                         ON unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                        AND unidade_medida.cod_unidade  = catalogo_item.cod_unidade

                    JOIN compras.julgamento
                         ON julgamento.exercicio    = cotacao.exercicio
                        AND julgamento.cod_cotacao  = cotacao.cod_cotacao
                     
                    JOIN compras.julgamento_item
                         ON julgamento_item.exercicio   = julgamento.exercicio
                        AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                        AND julgamento_item.cod_item    = mapa_item.cod_item 

                    JOIN licitacao.homologacao
                         ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                        AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                        AND homologacao.cod_entidade        = licitacao.cod_entidade
                        AND homologacao.exercicio_licitacao = licitacao.exercicio
                        AND homologacao.cod_item            = julgamento_item.cod_item
                        AND homologacao.lote                = julgamento_item.lote
                        AND (   SELECT homologacao_anulada.num_homologacao 
                                FROM licitacao.homologacao_anulada
                                WHERE homologacao_anulada.cod_licitacao     = licitacao.cod_licitacao
                                AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                                AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                                AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                                AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                                AND homologacao.cod_item                    = homologacao_anulada.cod_item
                                AND homologacao.lote                        = homologacao_anulada.lote
                         ) IS NULL
                                    
                    JOIN sw_cgm AS responsavel
                      ON responsavel.numcgm = participante.numcgm_representante

                    LEFT JOIN tcmgo.orgao
                        ON orgao.numcgm_contador = responsavel.numcgm
                      
                    JOIN ( SELECT num_documento, numcgm, tipo_documento
                            FROM (
                                    SELECT  cpf AS num_documento
                                            , numcgm
                                            , 1 AS tipo_documento
                                    FROM sw_cgm_pessoa_fisica
                                      
                                    UNION
                                     
                                    SELECT  cnpj AS num_documento
                                            , numcgm
                                            , 2 AS tipo_documento
                                    FROM sw_cgm_pessoa_juridica
                                ) AS tabela
                            GROUP BY numcgm, num_documento, tipo_documento
                        ) AS documento_pessoa
                      ON documento_pessoa.numcgm = julgamento_item.cgm_fornecedor
                                        
                    WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                    AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
                    AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                    AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                    AND licitacao.cod_modalidade NOT IN (8,9)
                    AND NOT EXISTS ( SELECT 1
                                     FROM licitacao.licitacao_anulada
                                     WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                     AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                     AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                     AND licitacao_anulada.exercicio = licitacao.exercicio
                        )
                      
                 GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13
                 ORDER BY num_processo_licitatorio
        ";
        return $stSql;
    }
    
    public function recuperaExportacao20(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao20",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao20()
    {
        $stSql = "  SELECT
                            20 AS tipo_registro
                            , LPAD(despesa.num_orgao::VARCHAR,2,'0') AS cod_orgao
                            , LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS cod_unidade
                            , licitacao.exercicio AS exercicio_licitacao
                            , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                            , documento_pessoa.tipo_documento AS tipo_documento
                            , documento_pessoa.num_documento AS num_documento
                            , mapa_item.lote AS num_lote
                            , mapa_item.cod_item AS cod_item
                            , 0.00 AS perc_desconto
                            , '' as brancos
                  
                    FROM licitacao.licitacao
                            
                    JOIN licitacao.participante
                      ON participante.cod_licitacao = licitacao.cod_licitacao
                     AND participante.cod_modalidade = licitacao.cod_modalidade
                     AND participante.cod_entidade = licitacao.cod_entidade
                     AND participante.exercicio = licitacao.exercicio
                    
                    JOIN compras.mapa
                      ON mapa.exercicio = licitacao.exercicio_mapa
                     AND mapa.cod_mapa = licitacao.cod_mapa
                     
                    JOIN compras.mapa_solicitacao
                      ON mapa_solicitacao.exercicio = mapa.exercicio
                     AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
                     
                    JOIN compras.mapa_item
                      ON mapa_item.exercicio = mapa_solicitacao.exercicio
                     AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
                     AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                     AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
                     AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                    
                    JOIN compras.mapa_cotacao
                      ON mapa_cotacao.exercicio_mapa = mapa.exercicio
                     AND mapa_cotacao.cod_mapa = mapa.cod_mapa
                     
                    JOIN compras.cotacao
                      ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
                     AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                    
                    JOIN compras.julgamento
                      ON julgamento.exercicio = cotacao.exercicio
                     AND julgamento.cod_cotacao = cotacao.cod_cotacao
                     
                    JOIN compras.mapa_item_dotacao
                      ON mapa_item_dotacao.exercicio = mapa_item.exercicio
                     AND mapa_item_dotacao.cod_entidade = mapa_item.cod_entidade
                     AND mapa_item_dotacao.cod_solicitacao = mapa_item.cod_solicitacao
                     AND mapa_item_dotacao.cod_mapa = mapa_item.cod_mapa
                     AND mapa_item_dotacao.cod_centro = mapa_item.cod_centro
                     AND mapa_item_dotacao.cod_item = mapa_item.cod_item
                     AND mapa_item_dotacao.lote = mapa_item.lote
                     AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                     AND mapa_item_dotacao.cod_entidade = mapa_item.cod_entidade
                     
                    JOIN compras.solicitacao_item_dotacao
                      ON solicitacao_item_dotacao.exercicio = mapa_item_dotacao.exercicio_solicitacao
                     AND solicitacao_item_dotacao.cod_entidade = mapa_item_dotacao.cod_entidade
                     AND solicitacao_item_dotacao.cod_solicitacao = mapa_item_dotacao.cod_solicitacao
                     AND solicitacao_item_dotacao.cod_centro = mapa_item_dotacao.cod_centro
                     AND solicitacao_item_dotacao.cod_item = mapa_item_dotacao.cod_item
                     AND solicitacao_item_dotacao.cod_conta = mapa_item_dotacao.cod_conta
                     AND solicitacao_item_dotacao.cod_despesa = mapa_item_dotacao.cod_despesa
             
                    JOIN orcamento.despesa
                      ON despesa.exercicio = solicitacao_item_dotacao.exercicio
                     AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
                          
                    JOIN sw_cgm AS responsavel
                      ON responsavel.numcgm = participante.numcgm_representante
                    
                    LEFT JOIN tcmgo.orgao
                        ON orgao.numcgm_contador = responsavel.numcgm
                    
                    JOIN ( SELECT num_documento, numcgm, tipo_documento
                             FROM (
                                    SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                                      FROM sw_cgm_pessoa_fisica
                                      
                                     UNION
                                     
                                    SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                                      FROM sw_cgm_pessoa_juridica
                                ) AS tabela
                            GROUP BY numcgm, num_documento, tipo_documento
                        ) AS documento_pessoa
                      ON documento_pessoa.numcgm = responsavel.numcgm
              
                    WHERE TO_DATE(TO_CHAR(licitacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                    AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
                    AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                    AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                    GROUP BY 1,2,3,4,5,6,7,8,9,10,11
        ";
        return $stSql;
    }
        
    public function recuperaExportacao30(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao30",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao30()
    {
        $stSql = "  SELECT
                            30 AS tipo_registro
                            , LPAD(licitacao.num_orgao::VARCHAR,2,'0') AS cod_orgao
                            , LPAD(licitacao.num_unidade::VARCHAR, 2, '0') AS cod_unidade
                            , licitacao.exercicio AS exercicio_licitacao
                            , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                            , TO_CHAR(julgamento.timestamp,'ddmmyyyy') AS dt_julgamento
                            , 1 AS presenca_licitantes
                            , CASE WHEN participante.renuncia_recurso = true THEN
                                        1
                                ELSE
                                        2
                            END AS renuncia_recurso 
                            , '' as brancos
                 
                    FROM licitacao.licitacao
                            
                    JOIN licitacao.participante
                      ON participante.cod_licitacao = licitacao.cod_licitacao
                     AND participante.cod_modalidade = licitacao.cod_modalidade
                     AND participante.cod_entidade = licitacao.cod_entidade
                     AND participante.exercicio = licitacao.exercicio
                    
                    JOIN compras.mapa
                      ON mapa.exercicio = licitacao.exercicio_mapa
                     AND mapa.cod_mapa = licitacao.cod_mapa
                     
                    JOIN compras.mapa_solicitacao
                      ON mapa_solicitacao.exercicio = mapa.exercicio
                     AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
                     
                    JOIN compras.mapa_item
                      ON mapa_item.exercicio = mapa_solicitacao.exercicio
                     AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
                     AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                     AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
                     AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                    
                    JOIN compras.mapa_cotacao
                      ON mapa_cotacao.exercicio_mapa = mapa.exercicio
                     AND mapa_cotacao.cod_mapa = mapa.cod_mapa
                     
                    JOIN compras.cotacao
                      ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
                     AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                    
                    JOIN compras.julgamento
                      ON julgamento.exercicio = cotacao.exercicio
                     AND julgamento.cod_cotacao = cotacao.cod_cotacao
            
                    JOIN compras.julgamento_item
                        ON julgamento_item.exercicio = julgamento.exercicio
                        AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                        AND julgamento_item.cod_item = mapa_item.cod_item                      
             
                    JOIN licitacao.homologacao
                      ON homologacao.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao.cod_entidade=licitacao.cod_entidade
                     AND homologacao.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.cod_item=julgamento_item.cod_item
                     AND homologacao.lote=julgamento_item.lote
                     AND (
                             SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                             WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                             AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                             AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                             AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                             AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                             AND homologacao.cod_item=homologacao_anulada.cod_item
                             AND homologacao.lote=homologacao_anulada.lote
                         ) IS NULL
             
                    JOIN sw_cgm AS responsavel
                      ON responsavel.numcgm = participante.numcgm_representante
                    
                    LEFT JOIN tcmgo.orgao
                        ON orgao.numcgm_contador = responsavel.numcgm
            
                    JOIN ( SELECT num_documento, numcgm, tipo_documento
                             FROM (
                                    SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                                      FROM sw_cgm_pessoa_fisica
                                      
                                     UNION
                                     
                                    SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                                      FROM sw_cgm_pessoa_juridica
                                ) AS tabela
                            GROUP BY numcgm, num_documento, tipo_documento
                        ) AS documento_pessoa
                      ON documento_pessoa.numcgm = responsavel.numcgm
              
                    WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                    AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
                    AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                    AND licitacao.cod_entidade IN (" . $this->getDado('entidades'). ")
                    AND licitacao.cod_modalidade NOT IN (8,9)
                    AND NOT EXISTS( SELECT 1
                                    FROM licitacao.licitacao_anulada
                                    WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                    AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                    AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                    AND licitacao_anulada.exercicio = licitacao.exercicio
                                ) 
            GROUP BY tipo_registro, cod_orgao, cod_unidade, exercicio_licitacao, num_processo_licitatorio, dt_julgamento, presenca_licitantes, renuncia_recurso, licitacao.exercicio
            ORDER BY num_processo_licitatorio
        ";
        return $stSql;
    }
    
    public function __destruct(){}
    
}