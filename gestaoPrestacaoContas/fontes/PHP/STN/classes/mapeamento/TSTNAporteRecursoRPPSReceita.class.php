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
 * Mapeamento da tabela stn.aporte_recurso_rpps
 *
 * @category    Urbem
 * @package     STN
 * @author      Desenvolvedor   Davi Ritter Aroldi
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TSTNAporteRecursoRPPSReceita extends Persistente
{
    /**
     * Método Construtor da classe TSTNAporteRecursoRPPSReceita
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('stn.aporte_recurso_rpps_receita');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_aporte,cod_receita,exercicio');

        $this->AddCampo('cod_aporte'   , 'integer', true, ''    , true , false);
        $this->AddCampo('cod_receita'  , 'integer', true, ''    , true , false);
        $this->AddCampo('exercicio'    , 'varchar', true, '4'   , true , false);
    }

    /**
     * Método que retorna os vínculos dos aportes com a receita
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarVinculoAporteReceita(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT cod_aporte
                 , receita.cod_receita
                 , cod_estrutural AS mascara_classificacao
                 , descricao
                 , receita.exercicio
              FROM orcamento.receita
        INNER JOIN orcamento.conta_receita
                ON conta_receita.cod_conta = receita.cod_conta
               AND conta_receita.exercicio = receita.exercicio
        INNER JOIN stn.aporte_recurso_rpps_receita
                ON aporte_recurso_rpps_receita.cod_receita = receita.cod_receita
               AND aporte_recurso_rpps_receita.exercicio = receita.exercicio
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
}
