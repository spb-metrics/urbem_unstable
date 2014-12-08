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

    * Extensão da Classe de Mapeamento TTCEALInfoRemessa
    *
    * Data de Criação: 30/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: TTCEALInfoRemessa.class.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * @ignore
    *
*/
class TTCEALInfoRemessa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALInfoRemessa()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
       
    public function listarExportacaoInfoRemessa(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
        $stSql = "SELECT sw_cgm_pessoa_juridica.cnpj as cod_und_gestora
                                  , (SELECT lpad(valor,4,'0') as valor
                                        FROM administracao.configuracao_entidade
                                      WHERE configuracao_entidade.cod_modulo = 62
                                           AND configuracao_entidade.exercicio = '".$this->getDado('stExercicio')."'
                                           AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                                           AND configuracao_entidade.cod_entidade =  ".$this->getDado('inCodEntidade')."
                                     ) AS codigo_ua

                    FROM contabilidade.plano_banco

              INNER JOIN contabilidade.plano_analitica
                      ON plano_analitica.exercicio = plano_banco.exercicio
                     AND plano_analitica.cod_plano = plano_banco.cod_plano

              INNER JOIN contabilidade.plano_conta
                      ON plano_conta.exercicio = plano_analitica.exercicio
                     AND plano_conta.cod_conta = plano_analitica.cod_conta

              INNER JOIN contabilidade.plano_recurso
                      ON plano_recurso.exercicio = plano_analitica.exercicio
                     AND plano_recurso.cod_plano = plano_analitica.cod_plano

              INNER JOIN orcamento.recurso
                      ON recurso.exercicio = plano_recurso.exercicio
                     AND recurso.cod_recurso = plano_recurso.cod_recurso

              INNER JOIN orcamento.despesa
                      ON despesa.exercicio = recurso.exercicio
                     AND despesa.cod_recurso = recurso.cod_recurso

              INNER JOIN orcamento.entidade
                      ON entidade.exercicio = despesa.exercicio
                     AND entidade.cod_entidade = despesa.cod_entidade

               LEFT JOIN sw_cgm_pessoa_juridica
                      ON sw_cgm_pessoa_juridica.numcgm = entidade.numcgm

                   WHERE plano_analitica.exercicio ='".$this->getDado('stExercicio')."'
                     AND despesa.cod_entidade IN (".$this->getDado('inCodEntidade').")

                GROUP BY cod_und_gestora
                       , codigo_ua";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }
     
}
?>
